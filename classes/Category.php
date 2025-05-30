<?php
require_once 'Model.php';

class Category extends Model {
    protected $table = 'categories';

    public function __construct() {
        parent::__construct();
    }

    public function getAll() {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.status = 'active') as product_count 
                FROM {$this->table} c 
                ORDER BY c.name ASC";
        $result = $this->db->query($sql);
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    public function getMainCategories() {
        return $this->where('parent_id IS NULL');
    }

    public function getSubCategories($parentId) {
        return $this->where('parent_id = ?', [$parentId]);
    }

    public function getCategoryTree() {
        $categories = $this->all();
        $tree = [];
        
        // First, index all categories by their ID
        $indexed = [];
        foreach ($categories as $category) {
            $indexed[$category['id']] = $category;
            $indexed[$category['id']]['children'] = [];
        }
        
        // Then, build the tree
        foreach ($categories as $category) {
            if ($category['parent_id'] === null) {
                $tree[] = &$indexed[$category['id']];
            } else {
                $indexed[$category['parent_id']]['children'][] = &$indexed[$category['id']];
            }
        }
        
        return $tree;
    }

    public function getCategoryPath($categoryId) {
        $path = [];
        $category = $this->find($categoryId);
        
        while ($category) {
            array_unshift($path, $category);
            $category = $category['parent_id'] ? $this->find($category['parent_id']) : null;
        }
        
        return $path;
    }

    public function getCategoryWithProductCount() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id 
                WHERE p.status = 'active' 
                GROUP BY c.id";
        $result = $this->db->query($sql);
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    public function moveCategory($categoryId, $newParentId) {
        // Prevent circular reference
        if ($categoryId == $newParentId) {
            return false;
        }
        
        // Check if new parent is not a descendant of the category
        $path = $this->getCategoryPath($newParentId);
        foreach ($path as $ancestor) {
            if ($ancestor['id'] == $categoryId) {
                return false;
            }
        }
        
        return $this->update($categoryId, ['parent_id' => $newParentId]);
    }

    public function deleteCategory($categoryId) {
        // First, move all subcategories to parent category
        $category = $this->find($categoryId);
        if ($category) {
            $subCategories = $this->getSubCategories($categoryId);
            foreach ($subCategories as $subCategory) {
                $this->update($subCategory['id'], ['parent_id' => $category['parent_id']]);
            }
        }
        
        // Then delete the category
        return $this->delete($categoryId);
    }
} 