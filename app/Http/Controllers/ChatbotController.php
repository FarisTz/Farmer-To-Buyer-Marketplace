<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatbotKnowledge;
use App\Models\ChatbotConversation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Display chatbot index page (not used in widget)
     */
    public function index()
    {
        return response()->json([
            'message' => 'Chatbot API is working',
            'status' => 'active'
        ]);
    }

    /**
     * Handle chatbot message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->message;
        $sessionId = $request->session()->get('chatbot_session_id', Str::random(32));
        $request->session()->put('chatbot_session_id', $sessionId);
        
        // Search for relevant knowledge
        $knowledge = $this->searchKnowledge($userMessage);
        
        // Generate response
        $response = $knowledge 
            ? $knowledge->answer 
            : $this->generateDefaultResponse($userMessage);

        // Detect intent
        $intent = $this->detectIntent($userMessage);
        
        // Calculate confidence score
        $confidence = $knowledge ? $this->calculateConfidence($userMessage, $knowledge) : 0;

        // Log the conversation
        $conversation = ChatbotConversation::create([
            'session_id' => $sessionId,
            'user_id' => Auth::id(),
            'user_message' => $userMessage,
            'bot_response' => $response,
            'knowledge_id' => $knowledge ? $knowledge->id : null,
            'intent' => $intent,
            'confidence_score' => $confidence,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Log::info('Chatbot interaction', [
            'conversation_id' => $conversation->id,
            'user_message' => $userMessage,
            'bot_response' => $response,
            'knowledge_used' => $knowledge ? $knowledge->id : null,
            'intent' => $intent,
            'confidence' => $confidence,
        ]);

        return response()->json([
            'success' => true,
            'response' => $response,
            'conversation_id' => $conversation->id,
            'intent' => $intent,
            'confidence' => $confidence,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Submit user feedback for a conversation
     */
    public function submitFeedback(Request $request, $conversationId)
    {
        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'was_helpful' => 'nullable|boolean',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $conversation = ChatbotConversation::findOrFail($conversationId);
        
        $conversation->update([
            'user_rating' => $request->rating,
            'was_helpful' => $request->was_helpful,
            'user_feedback' => $request->feedback,
        ]);

        // Update knowledge base performance if this conversation used knowledge
        if ($conversation->knowledge_id) {
            $this->updateKnowledgePerformance($conversation->knowledge_id, $request->was_helpful);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!'
        ]);
    }

    /**
     * Get chatbot analytics
     */
    public function getAnalytics()
    {
        $totalConversations = ChatbotConversation::count();
        $successRate = ChatbotConversation::getSuccessRate();
        $averageRating = ChatbotConversation::getAverageRating();
        
        $topIntents = ChatbotConversation::select('intent', \DB::raw('count(*) as count'))
            ->whereNotNull('intent')
            ->groupBy('intent')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $recentConversations = ChatbotConversation::with('knowledge')
            ->recent(7)
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'total_conversations' => $totalConversations,
            'success_rate' => round($successRate, 2),
            'average_rating' => round($averageRating, 2),
            'top_intents' => $topIntents,
            'recent_conversations' => $recentConversations,
        ]);
    }

    /**
     * Search knowledge base for relevant answers
     */
    private function searchKnowledge($message)
    {
        // Clean and normalize message
        $cleanMessage = strtolower(trim($message));
        
        // Extract keywords from message
        $keywords = $this->extractKeywords($cleanMessage);
        
        // Search in knowledge base
        foreach ($keywords as $keyword) {
            $knowledge = ChatbotKnowledge::active()
                ->searchByKeywords($keyword)
                ->first();
                
            if ($knowledge) {
                return $knowledge;
            }
        }
        
        return null;
    }

    /**
     * Extract keywords from user message
     */
    private function extractKeywords($message)
    {
        // Remove common words and extract potential keywords
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can'];
        $words = array_diff(explode(' ', $message), $stopWords);
        
        // Return unique words longer than 2 characters
        return array_unique(array_filter($words, function($word) {
            return strlen($word) > 2;
        }));
    }

    /**
     * Detect user intent from message
     */
    private function detectIntent($message)
    {
        $message = strtolower($message);
        
        $intents = [
            'greeting' => ['hello', 'hi', 'hey', 'greetings', 'good morning', 'good afternoon'],
            'help' => ['help', 'assist', 'support', 'guidance', 'how to'],
            'registration' => ['register', 'signup', 'sign up', 'create account', 'join'],
            'verification' => ['verify', 'verification', 'documents', 'id', 'proof'],
            'crops' => ['crop', 'crops', 'sell', 'listing', 'marketplace', 'farm'],
            'orders' => ['order', 'orders', 'buy', 'purchase', 'delivery', 'track'],
            'payment' => ['pay', 'payment', 'bank', 'transfer', 'money', 'receipt'],
            'contact' => ['contact', 'support', 'email', 'phone', 'call'],
            'goodbye' => ['bye', 'goodbye', 'farewell', 'see you', 'later'],
        ];

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $intent;
                }
            }
        }

        return 'general';
    }

    /**
     * Calculate confidence score for knowledge match
     */
    private function calculateConfidence($message, $knowledge)
    {
        $messageWords = $this->extractKeywords(strtolower($message));
        $knowledgeWords = array_merge(
            explode(' ', strtolower($knowledge->question ?? '')),
            explode(' ', strtolower($knowledge->keywords ?? ''))
        );
        
        $matches = 0;
        foreach ($messageWords as $word) {
            foreach ($knowledgeWords as $kWord) {
                if (str_contains($kWord, $word) || str_contains($word, $kWord)) {
                    $matches++;
                    break;
                }
            }
        }

        $confidence = count($messageWords) > 0 ? ($matches / count($messageWords)) * 100 : 0;
        return min(100, max(0, $confidence));
    }

    /**
     * Generate default responses when no knowledge is found
     */
    private function generateDefaultResponse($message)
    {
        $intent = $this->detectIntent($message);
        
        $responses = [
            'greeting' => 'Hello! I\'m here to help you with questions about the FarmMarket platform. How can I assist you today?',
            'help' => 'I can help you with:\n\n' . 
                      '1. Account registration and verification\n' .
                      '2. Crop listings and sales\n' .
                      '3. Order management and tracking\n' .
                      '4. Payment processing\n' .
                      '5. Platform features and support\n\n' .
                      'What would you like to know more about?',
            'registration' => 'To get started with FarmMarket:\n\n' .
                              '1. Click "Register" in the top menu\n' .
                              '2. Choose your role (Farmer or Buyer)\n' .
                              '3. Fill in your personal details\n' .
                              '4. Complete account verification\n' .
                              '5. Start using the platform!\n\n' .
                              'Would you like more specific guidance?',
            'verification' => 'Account verification requires:\n\n' .
                             '1. ID verification (national ID, passport, or license)\n' .
                             '2. Address verification (utility bill, lease, or bank statement)\n' .
                             '3. Phone number verification\n\n' .
                             'All documents must be clear and recent. Need help with verification?',
            'crops' => 'For crop management:\n\n' .
                      '1. Complete account verification first\n' .
                      '2. Go to "My Crops" in dashboard\n' .
                      '3. Click "Add New Crop"\n' .
                      '4. Fill in crop details and images\n' .
                      '5. Set pricing and availability\n\n' .
                      'What specific crop information do you need?',
            'orders' => 'Order management includes:\n\n' .
                       '1. Browse available crops\n' .
                       '2. Add items to cart\n' .
                       '3. Complete checkout\n' .
                       '4. Track delivery status\n' .
                       '5. Communicate with farmers\n\n' .
                       'How can I help with your orders?',
            'payment' => 'Payment options include:\n\n' .
                        '1. Bank transfer to farmer\'s account\n' .
                        '2. Mobile money transfers\n' .
                        '3. Cash on delivery\n\n' .
                        'Farmers must be verified to receive payments. Need payment help?',
            'contact' => 'Support channels:\n\n' .
                        '1. Use this chat assistant\n' .
                        '2. Email: support@farmmarket.com\n' .
                        '3. Phone: +255 123 456 789\n' .
                        '4. Report issues in your dashboard\n\n' .
                        'How can I assist you today?',
            'goodbye' => 'Thank you for using FarmMarket! Feel free to come back anytime if you need help. Have a great day!',
        ];

        return $responses[$intent] ?? 
               'I understand you\'re asking about: "' . $message . '". Let me help you with that. Could you provide more details about what specific information you need?';
    }

    /**
     * Update knowledge base performance based on feedback
     */
    private function updateKnowledgePerformance($knowledgeId, $wasHelpful)
    {
        $knowledge = ChatbotKnowledge::find($knowledgeId);
        if ($knowledge) {
            // This could be expanded to track performance metrics
            // For now, we'll just log it
            Log::info('Knowledge performance updated', [
                'knowledge_id' => $knowledgeId,
                'was_helpful' => $wasHelpful,
            ]);
        }
    }
}
