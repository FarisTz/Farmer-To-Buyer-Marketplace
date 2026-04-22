import 'package:flutter/material.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../models/crop.dart';
import '../services/api_service.dart';
import 'crop_details_screen.dart';

class CropsScreen extends StatefulWidget {
  const CropsScreen({Key? key}) : super(key: key);

  @override
  _CropsScreenState createState() => _CropsScreenState();
}

class _CropsScreenState extends State<CropsScreen> {
  List<Crop> _crops = [];
  List<Category> _categories = [];
  bool _isLoading = true;
  bool _isLoadingMore = false;
  String? _selectedCategory;
  String _searchQuery = '';
  int _currentPage = 1;
  int _lastPage = 1;
  bool _hasMore = true;

  final TextEditingController _searchController = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _loadData();
    _scrollController.addListener(_scrollListener);
  }

  @override
  void dispose() {
    _searchController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollListener() {
    if (_scrollController.position.pixels == _scrollController.position.maxScrollExtent) {
      if (!_isLoadingMore && _hasMore) {
        _loadMoreCrops();
      }
    }
  }

  Future<void> _loadData() async {
    await Future.wait([
      _loadCategories(),
      _loadCrops(refresh: true),
    ]);
  }

  Future<void> _loadCategories() async {
    try {
      final result = await ApiService.getCategories();
      if (result['success']) {
        setState(() {
          _categories = (result['data']['categories'] as List)
              .map((category) => Category.fromJson(category))
              .toList();
        });
      }
    } catch (e) {
      print('Error loading categories: $e');
    }
  }

  Future<void> _loadCrops({bool refresh = false}) async {
    if (refresh) {
      setState(() {
        _isLoading = true;
        _currentPage = 1;
        _hasMore = true;
      });
    } else {
      setState(() {
        _isLoadingMore = true;
      });
    }

    try {
      final result = await ApiService.getCrops(
        category: _selectedCategory,
        search: _searchQuery.isNotEmpty ? _searchQuery : null,
        page: _currentPage,
      );

      if (result['success']) {
        final data = result['data'];
        final newCrops = (data['crops'] as List)
            .map((crop) => Crop.fromJson(crop))
            .toList();

        setState(() {
          if (refresh) {
            _crops = newCrops;
          } else {
            _crops.addAll(newCrops);
          }
          
          final pagination = data['pagination'];
          _lastPage = pagination['last_page'];
          _hasMore = _currentPage < _lastPage;
          _isLoading = false;
          _isLoadingMore = false;
        });
      } else {
        setState(() {
          _isLoading = false;
          _isLoadingMore = false;
        });
        Fluttertoast.showToast(
          msg: result['message'] ?? 'Failed to load crops',
          backgroundColor: Colors.red,
        );
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _isLoadingMore = false;
      });
      Fluttertoast.showToast(
        msg: 'Error loading crops',
        backgroundColor: Colors.red,
      );
    }
  }

  Future<void> _loadMoreCrops() async {
    if (_hasMore && !_isLoadingMore) {
      setState(() {
        _currentPage++;
      });
      await _loadCrops();
    }
  }

  void _onSearchChanged(String query) {
    setState(() {
      _searchQuery = query;
      _currentPage = 1;
    });
    _loadCrops(refresh: true);
  }

  void _onCategoryChanged(String? category) {
    setState(() {
      _selectedCategory = category;
      _currentPage = 1;
    });
    _loadCrops(refresh: true);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          // Search and Filter Section
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.green[50],
            child: Column(
              children: [
                // Search Bar
                TextField(
                  controller: _searchController,
                  onChanged: _onSearchChanged,
                  decoration: InputDecoration(
                    hintText: 'Search crops...',
                    prefixIcon: const Icon(Icons.search),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                    filled: true,
                    fillColor: Colors.white,
                  ),
                ),
                
                const SizedBox(height: 12),
                
                // Category Filter
                SizedBox(
                  height: 50,
                  child: ListView(
                    scrollDirection: Axis.horizontal,
                    children: [
                      // All Categories
                      Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          label: const Text('All'),
                          selected: _selectedCategory == null,
                          onSelected: (selected) {
                            _onCategoryChanged(null);
                          },
                          backgroundColor: Colors.white,
                          selectedColor: Colors.green[700],
                          labelStyle: TextStyle(
                            color: _selectedCategory == null ? Colors.white : Colors.black,
                          ),
                        ),
                      ),
                      
                      // Category Chips
                      ..._categories.map((category) {
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: FilterChip(
                            label: Text(category.name),
                            selected: _selectedCategory == category.id.toString(),
                            onSelected: (selected) {
                              _onCategoryChanged(category.id.toString());
                            },
                            backgroundColor: Colors.white,
                            selectedColor: Colors.green[700],
                            labelStyle: TextStyle(
                              color: _selectedCategory == category.id.toString() 
                                  ? Colors.white 
                                  : Colors.black,
                            ),
                          ),
                        );
                      }).toList(),
                    ],
                  ),
                ),
              ],
            ),
          ),
          
          // Crops List
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _crops.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.inventory_2_outlined,
                              size: 64,
                              color: Colors.grey[400],
                            ),
                            const SizedBox(height: 16),
                            Text(
                              'No crops found',
                              style: TextStyle(
                                fontSize: 18,
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: () => _loadCrops(refresh: true),
                        child: ListView.builder(
                          controller: _scrollController,
                          padding: const EdgeInsets.all(16),
                          itemCount: _crops.length + (_hasMore ? 1 : 0),
                          itemBuilder: (context, index) {
                            if (index == _crops.length) {
                              return const Center(
                                child: Padding(
                                  padding: EdgeInsets.all(16),
                                  child: CircularProgressIndicator(),
                                ),
                              );
                            }
                            
                            final crop = _crops[index];
                            return CropCard(crop: crop);
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }
}

class CropCard extends StatelessWidget {
  final Crop crop;

  const CropCard({
    Key? key,
    required this.crop,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 4,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        onTap: () {
          Navigator.of(context).push(
            MaterialPageRoute(
              builder: (context) => CropDetailsScreen(crop: crop),
            ),
          );
        },
        borderRadius: BorderRadius.circular(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image Section
            if (crop.image != null)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
                child: CachedNetworkImage(
                  imageUrl: 'http://127.0.0.1:8000/storage/crops/${crop.image}',
                  height: 180,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => Container(
                    height: 180,
                    color: Colors.grey[200],
                    child: const Center(child: CircularProgressIndicator()),
                  ),
                  errorWidget: (context, url, error) => Container(
                    height: 180,
                    color: Colors.grey[200],
                    child: Icon(
                      Icons.image_not_supported,
                      size: 64,
                      color: Colors.grey[400],
                    ),
                  ),
                ),
              ),
            
            // Content Section
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Name and Price
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          crop.name,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.green[700],
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          'TSH ${crop.pricePerKg.toStringAsFixed(2)}/${crop.unit}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                      ),
                    ],
                  ),
                  
                  const SizedBox(height: 8),
                  
                  // Description
                  Text(
                    crop.description,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 14,
                    ),
                  ),
                  
                  const SizedBox(height: 12),
                  
                  // Location and Quantity
                  Row(
                    children: [
                      Icon(
                        Icons.location_on,
                        size: 16,
                        color: Colors.grey[600],
                      ),
                      const SizedBox(width: 4),
                      Text(
                        crop.region,
                        style: TextStyle(
                          color: Colors.grey[600],
                          fontSize: 14,
                        ),
                      ),
                      const Spacer(),
                      Icon(
                        Icons.inventory_2,
                        size: 16,
                        color: Colors.grey[600],
                      ),
                      const SizedBox(width: 4),
                      Text(
                        '${crop.availableQuantity} ${crop.unit} available',
                        style: TextStyle(
                          color: Colors.grey[600],
                          fontSize: 14,
                        ),
                      ),
                    ],
                  ),
                  
                  const SizedBox(height: 8),
                  
                  // Farmer Info
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 16,
                        backgroundColor: Colors.green[100],
                        child: Icon(
                          Icons.person,
                          size: 16,
                          color: Colors.green[700],
                        ),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        crop.farmer.name,
                        style: const TextStyle(
                          fontWeight: FontWeight.w500,
                          fontSize: 14,
                        ),
                      ),
                      const Spacer(),
                      if (crop.farmer.isVerified)
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.blue[100],
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                Icons.verified,
                                size: 12,
                                color: Colors.blue[700],
                              ),
                              const SizedBox(width: 4),
                              Text(
                                'Verified',
                                style: TextStyle(
                                  fontSize: 10,
                                  color: Colors.blue[700],
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
