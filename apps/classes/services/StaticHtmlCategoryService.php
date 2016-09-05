<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
class StaticHtmlCategoryService extends aafwServiceBase {

    /** @var aafwEntityStoreBase $categories */
	private $categories;
    /** @var aafwEntityStoreBase $category_relations */
    private $category_relations;
    /** @var aafwEntityStoreBase $entry_category */
    private $entry_category;
    /** @var aafwEntityStoreBase $category_sns_plugin */
    private $category_sns_plugin;
    private $end_key;
    private $logger;


	public function __construct() {
		$this->categories = $this->getModel("StaticHtmlCategories");
        $this->category_relations = $this->getModel("StaticHtmlCategoryRelations");
        $this->entry_category = $this->getModel("StaticHtmlEntryCategories");
        $this->category_sns_plugin = $this->getModel("StaticHtmlCategorySnsPlugins");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->end_key = md5('end_categories');
	}

    /**
     * @param $depth
     * @param $brand_id
     * @return aafwEntityContainer|array
     */
    public function getCategoriesAtDepth($depth, $brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'depth'=>$depth
            ),
            'order' => array(
                'name' => 'order_no'
            )
        );
        return $this->categories->find($filter);
    }

    /**
     * @param null $category_id
     * @param $brand_id
     * @return array|string
     */
    public function getCategoriesTree($brand_id, $category_id=null) {
        if (!$category_id) {
            $first_categories = $this->getCategoriesAtDepth(0, $brand_id);
            $tree = array();
            foreach ($first_categories as $first_category) {
                $tree[$first_category->id] = $this->getCategoriesTree($brand_id, $first_category->id);
            }
            return $tree;
        } elseif (!$this->hasChildren($category_id)) {
            return $this->end_key;
        } else {
            $children = $this->getAllChildrenOfCategory($category_id);
            $tree = array();
            foreach ($children as $child) {
                $tree[$child] = $this->getCategoriesTree($brand_id, $child);
            }
            return $tree;
        }
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function hasChildren($category_id) {
        return $this->category_relations->findOne(array('parent_id'=>$category_id)) ? true : false;
    }

    /**
     * @param $category_name
     * @param $brand_id
     * @return entity
     */
    public function getCategoryByNameAndBrandId($category_name, $brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'name' => $category_name
            )
        );
        return $this->categories->findOne($filter);
    }

    /**
     * @param $directory
     * @param $brand_id
     * @return entity
     */
    public function getCategoryByDirectoryAndBrandId($directory, $brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
                'directory' => $directory
            )
        );
        return $this->categories->findOne($filter);
    }

    /**
     * @param $tab_id
     * @return array
     */
    public function getAllChildrenOfCategory($tab_id) {
        $children_nodes = $this->category_relations->find(array('parent_id' => $tab_id));
        $children = array();
        foreach ($children_nodes as $children_node) {
            $children[] = $children_node->children_id;
        }
        if (count($children) > 0) {
            $children = $this->orderChildren($children);
        }
        return $children;
    }

    //並べる
    /**
     * @param $children
     * @return array
     */
    public function orderChildren($children) {
        $filter = array(
            'conditions' => array(
                'id' => $children
            ),
            'order' => array(
                'name' => 'order_no'
            )
        );
        $children_nodes = $this->categories->find($filter);
        $result = array();
        foreach ($children_nodes as $children_node) {
            $result[] = $children_node->id;
        }
        return $result;
    }

    /**
     * @param $category_id
     * @return entity
     */
    public function getCategoryById ($category_id) {
        return $this->categories->findOne($category_id);
    }

    public function deleteAndSortCategory ($category_id) {
        $category = $this->getCategoryById($category_id);
        $this->deleteCategoryTree($category);
        $this->reduceOrderAfterNode($category);
    }

    public function deleteCategoryTree (StaticHtmlCategory $category) {
        $children = $this->getAllChildrenOfCategory($category->id);
        if (!count($children)) {
            $this->deleteCategory($category);
            return;
        }
        foreach ($children as $child) {
            $child_node = $this->getCategoryById($child);
            $this->deleteCategoryTree($child_node);
        }
        $this->deleteCategory($category);
    }

    public function deleteCategory (StaticHtmlCategory $category) {
        $this->category_relations->begin();
        $relation = $this->category_relations->findOne(array('children_id'=>$category->id));
        if ($relation) {
            $this->category_relations->deletePhysical($relation);
        }
        $this->categories->deletePhysical($category);
        $this->category_relations->commit();
    }

    /**
     * @param $current_node
     */
    public function reduceOrderAfterNode(StaticHtmlCategory $current_node) {
        $old_relation = $this->category_relations->findOne(array('children_id' => $current_node->id));
        if ($old_relation) {
            $p_children = $this->getAllChildrenOfCategory($old_relation->parent_id);
            $this->categories->begin();
            foreach ($p_children as $child) {
                $child_node = $this->getCategoryById($child);
                if ($child_node->order_no > $current_node->order_no) {
                    $child_node->order_no -= 1;
                    $this->categories->save($child_node);
                }
            }
            $this->categories->commit();
        } else {
            $children = $this->getCategoriesAtDepth(0, $current_node->brand_id);
            $this->categories->begin();
            foreach ($children as $child) {
                if ($child->order_no > $current_node->order_no) {
                    $child->order_no -= 1;
                    $this->categories->save($child);
                }
            }
            $this->categories->commit();
        }
    }

    /**
     * @param $post
     * @param $brand_id
     * @return null
     */
    public function createCategory($post, $brand_id) {
        try {
            $this->categories->begin();
            if (!$post['parent_id']) {
                $depth = 0;
            } else {
                $parent_category = $this->getCategoryById($post['parent_id']);

                $children = $this->getAllChildrenOfCategory($post['parent_id']);
                foreach ($children as $child) {
                    $child_node = $this->getCategoryById($child);
                    $child_node->order_no += 1;
                    try {
                        $this->categories->save($child_node);
                    } catch (Exception $e) {
                        //$childrenでsaveした新しいカテゴリーがあったらsaveが重複になります
                        //コミットする前に重複のsaveのエラーなのでなんでもしないです。
                    }
                }
                $depth = $parent_category->depth + 1;
            }

            $new_category = $this->categories->createEmptyObject();
            $new_category->brand_id = $brand_id;
            $new_category->depth = $depth;
            $new_category->order_no = $post['order'] ? $post['order'] : 0;
            $new_category->name = $post['name'];
            if ($this->isEmpty($post['directory'])) {
                if (ctype_alnum($post['name'])) {
                    $new_category->directory = $post['name'];
                } else {
                    $new_category->directory = $this->createRandomDirectory();
                    if($this->getCategoryByDirectoryAndBrandId($new_category->directory,$brand_id)) throw new Exception('ディレクトリー名が同一階層内で重複しています');
                }
            } else {
                $new_category->directory = $post['directory'];
            }
            $new_category->title = $post['title'] ? $post['title'] : '';
            $new_category->description = $post['description'] ? $post['description'] : '';
            $new_category->keyword = $post['keyword'] ? $post['keyword'] : '';
            $new_category->og_image_url = $post['og_image_url'];
            $new_category->is_use_customize = $post['is_use_customize'] ? $post['is_use_customize'] : 0;
            $new_category->customize_code = $post['customize_code'] ? $post['customize_code'] : '';
            $new_category->sns_plugin_tag_text = $post['sns_plugin_tag_text'] ? $post['sns_plugin_tag_text'] : '';
            $this->categories->save($new_category);

            if ($post['parent_id']) {
                $this->createCategoryRelation($post['parent_id'], $new_category->id);
            }
            $this->categories->commit();

            return $new_category;
        } catch (Exception $e) {
            $this->categories->rollback();
            $this->logger->error('StaticHtmlService createCategory could not add category name '.$post['name'].'brand_id = '.$brand_id);
            $this->logger->error($e);
            return null;
        }
    }

    public function updateCategory($post, $category_id, $brand_id) {
        $category = $this->getCategoryById($category_id);
        $category_relation = $this->category_relations->findOne(array('children_id' => $category_id));

        try {
            $this->categories->begin();
            // 古い関係を消す
            if ($category_relation && ($category_relation->parent_id != $post['parent_id'])) {
                $this->category_relations->deletePhysical($category_relation);
            }

            if (!$post['parent_id']) {
                $depth = 0;
                $top_categories = $this->getCategoriesAtDepth(0, $brand_id);
                foreach ($top_categories as $top_category) {
                    $top_category->order_no += 1;
                    $this->categories->save($top_category);
                }
            } else if ($post['parent_id'] != $category_relation->parent_id) {
                $this->createCategoryRelation($post['parent_id'], $category->id);

                $parent_category = $this->getCategoryById($post['parent_id']);

                $children = $this->getAllChildrenOfCategory($post['parent_id']);
                foreach ($children as $child) {
                    $child_node = $this->getCategoryById($child);
                    $child_node->order_no += 1;
                    $this->categories->save($child_node);
                }
                $depth = $parent_category->depth + 1;
            }

            $category->depth = $depth;
            $category->order_no = 0;
            $category->name = $post['name'];
            if ($this->isEmpty($post['directory'])) {
                if (ctype_alnum($post['name'])) {
                    $category->directory = $post['name'];
                } else {
                    $category->directory = $this->createRandomDirectory();
                    if ($this->getCategoryByDirectoryAndBrandId($category->directory,$brand_id)) throw new Exception('ディレクトリー名が同一階層内で重複しています');
                }
            } else {
                $category->directory = $post['directory'];
            }
            $category->title = $post['title'] ? $post['title'] : '';
            $category->description = $post['description'] ? $post['description'] : '';
            $category->keyword = $post['keyword'] ? $post['keyword'] : '';
            $category->og_image_url = $post['og_image_url'];
            $category->is_use_customize = $post['is_use_customize'] ? $post['is_use_customize'] : 0;
            $category->customize_code = $post['customize_code'] ? $post['customize_code'] : '';
            $category->sns_plugin_tag_text = $post['sns_plugin_tag_text'] ? $post['sns_plugin_tag_text'] : '';

            $this->categories->save($category);

            $this->categories->commit();

            return $category;
        } catch (Exception $e) {
            $this->categories->rollback();
            $this->logger->error('StaticHtmlService updateCategory '.$post['name'].'brand_id = '.$category->brand_id);
            $this->logger->error($e);
            return null;
        }
    }

    /**
     * @param int $length
     * @return string
     * @TODO 重複チェックをここでやる
     */
    public function createRandomDirectory ($length = 6) {
        $str = null;
        for ($i=0; $i < $length; $i++) {
            $str .= mt_rand(0,9);
        }
        return 'ctg'. $str;
    }

    /**
     * @param $parent_id
     * @param $brand_id
     * @return int
     */
    public function getNextOrderInChildren($parent_id, $brand_id) {
        if ($parent_id) {
            $children = $this->getAllChildrenOfCategory($parent_id);
        } else {
            $children = $this->getCategoriesAtDepth(0, $brand_id);
        }
        if ($children) {
            $max = 0;
            foreach ($children as $child) {
                $max = ($max < $child->order_no) ?  $child->order_no : $max;
            }
            return ++$max;
        } else {
            return 0;
        }
    }

    /**
     * @param $new_parent_id
     * @param $category_id
     * @param $order
     * @param null $next_id
     */
    public function moveCategory($new_parent_id, $category_id, $order, $next_id = null) {
        try {

            $category = $this->getCategoryById($category_id);
            //関係nodeのorder更新
            $this->updateOrderByNode($category, $order, $next_id, $new_parent_id);

            //カテゴリ情報更新
            if (!$new_parent_id) {
                $category->depth = 0;
            } else {
                $parent = $this->getCategoryById($new_parent_id);
                $category->depth = $parent->depth + 1;
            }
            $category->order_no = $order;
            $this->categories->save($category);

            //Childrenのdepth更新
            $this->updateDepthFromNode($category);

            //relation更新
            $old_relation = $this->category_relations->findOne(array('children_id' => $category_id));
            if ($old_relation) {
                $this->category_relations->deletePhysical($old_relation);
            }
            if ($new_parent_id) {
                $this->createCategoryRelation($new_parent_id, $category_id);
            }
        } catch (Exception $e) {
            $this->logger->error('moveCategory new_parent_id='.$new_parent_id.' category_id='.$category_id.' order='.$order);
            $this->logger->error($e);
        }
    }

    public function updateOrderByNode(StaticHtmlCategory $current_node, $new_order, $next_id, $new_parent_id) {
        //現在nodeの後のnodeのorderを更新
        $this->reduceOrderAfterNode($current_node);

        //新しい位置の後のnodeのorderを更新
        if ($next_id) {
            if ($new_parent_id) {
                $children = $this->getAllChildrenOfCategory($new_parent_id);
                $this->categories->begin();
                foreach ($children as $child) {
                    if ($child == $current_node->id) {
                        continue;
                    }
                    $child_node = $this->getCategoryById($child);
                    if ($child_node->order_no >= $new_order) {
                        $child_node->order_no += 1;
                        $this->categories->save($child_node);
                    }
                }
                $this->categories->commit();
            } else {
                $children = $this->getCategoriesAtDepth(0, $current_node->brand_id);
                $this->categories->begin();
                foreach ($children as $child) {
                    if ($child->id == $current_node->id) {
                        continue;
                    }
                    $child->order_no += 1;
                    $this->categories->save($child);
                }
                $this->categories->commit();
            }
        }
    }

    /**
     * @param StaticHtmlCategory $parent
     */
    public function updateDepthFromNode(StaticHtmlCategory $parent) {
        $children_notes = $this->getAllChildrenOfCategory($parent->id);
        if (!$children_notes) {
            return ;
        }
        foreach ($children_notes as $children_note) {
            $children = $this->getCategoryById($children_note);
            $children->depth = $parent->depth + 1;
            $this->categories->save($children);
            $this->updateDepthFromNode($children);
        }
    }

    /**
     * @param $parent_id
     * @param $children_id
     * @return mixed
     */
    public function createCategoryRelation($parent_id, $children_id) {
        $relation = $this->category_relations->createEmptyObject();
        $relation->parent_id = $parent_id;
        $relation->children_id = $children_id;
        return $this->category_relations->save($relation);
    }

    public function synchCategoriesByPost($post, $brand_id) {
        //カテゴリー削除
        $error_id = '';
        try {
            $this->categories->begin();

            if (is_array($post['deleted_categories'])) {
                foreach ($post['deleted_categories'] as $key => $val) {
                    $error_id = $val;
                    $this->deleteAndSortCategory($val);
                }
            }

            $this->categories->commit();

        } catch (Exception $e) {
            $this->logger->error('Cant delete category id='.$error_id);
            $e->entryId = $error_id;
            $e->errFunction = 'deleteAndSortCategory';
            $this->categories->rollback();
            throw $e;
        }

        //カテゴリー更新
        try {
            if (is_array($post['categories_data'])) {
                $this->updateCategoriesWithArray($post['categories_data'], $brand_id);
            }
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

    public function countNewCategories($category_ids) {
        $count = 0;
        foreach (array_keys($category_ids) as $key) {
            if (preg_match('/^new*/', $key)) {
                ++$count;
            }
        }
        return $count;
    }

    public function updateCategoriesWithArray($categories ,$brand_id ,$parent_id = 0) {
        $error_id = '';
        try {
            $this->categories->begin();
            foreach ($categories as $category_id => $value) {
                if ($this->isNumeric($category_id)) {
                    //更新
                    $category = $this->getCategoryById($category_id);
                    $category->order_no = $value['order']-$this->countNewCategories($categories);
                    $category_relation = $this->category_relations->findOne(array('children_id'=>$category_id));
                    if ($parent_id) {
                        $parent_category = $this->getCategoryById($parent_id);
                        $category->depth = $parent_category->depth + 1;
                        if ($category_relation) {
                            $category_relation->parent_id = $parent_id;
                        } else {
                            $category_relation = $this->category_relations->createEmptyObject();
                            $category_relation->parent_id = $parent_id;
                            $category_relation->children_id = $category_id;
                        }
                        $this->category_relations->save($category_relation);
                    } else if ($category_relation){
                        $category->depth = 0;
                        $this->category_relations->deletePhysical($category_relation);
                    }
                    $this->categories->save($category);

                } else {
                    //新規作成
                    if ($this->isNumeric(strpos($category_id, 'new_'))) {
                        $value['parent_id'] = $parent_id;
                        $category = $this->createCategory($value, $brand_id);
                        $category_id = $category->id;
                    }
                }
                if (is_array($value['children'])) {
                    $this->updateCategoriesWithArray($value['children'], $brand_id, $category_id);
                }
            }
            $this->categories->commit();
        } catch (Exception $e) {
            $this->categories->rollback();
            $this->logger('Cant update category id='.$error_id);
            $this->logger($e);
        }
    }

    /**
     * @param $categories
     * @param $brand_id
     * @return bool
     */
    public function isCorrectCategories($categories, $brand_id) {
        foreach ($categories as $category_id) {
            $category = $this->getCategoryById($category_id);
            if (!$category || $category->brand_id != $brand_id) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $tree
     * @param $brand_id
     * @return bool
     */
    public function isCorrectCategoriesTreeTypeJson($tree, $brand_id) {
        foreach ($tree as $category_id => $value) {
            if ($this->isNumeric($category_id)) {
                $category = $this->getCategoryById($category_id);
                if (!$category || $category->brand_id != $brand_id) {
                    return false;
                }
            } else {
                if (!$this->isNumeric(strpos($category_id, 'new_'))) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $entry_id
     * @param $category_id
     * @return mixed
     */
    public function createEntryCategory ($entry_id, $category_id) {
        $relation = $this->entry_category->createEmptyObject();
        $relation->static_html_entry_id = $entry_id;
        $relation->category_id = $category_id;
        return $this->entry_category->save($relation);
    }

    public function deleteEntryCategory ($entry_id, $category_id) {
        $object = $this->entry_category->findOne(array('static_html_entry_id' => $entry_id, 'category_id' => $category_id));
        $this->entry_category->deletePhysical($object);
    }

    /**
     * @param $category_id
     * @return entity|null
     */
    public function getParentOfCategory($category_id) {
        $relation = $this->category_relations->findOne(array('children_id' => $category_id));
        if ($relation) {
            return $this->getCategoryById($relation->parent_id);
        } else {
            return null;
        }

    }

    /**
     * @param StaticHtmlCategory $category
     * @return string|true
     */
    public function getDirectoryByCategory(StaticHtmlCategory $category) {
        $father = $this->getParentOfCategory($category->id);
        if ($father) {
            $grandfather = $this->getParentOfCategory($father->id);
            if ($grandfather) {
                return $grandfather->directory.'/'.$father->directory.'/'.$category->directory;
            } else {
                return $father->directory.'/'.$category->directory;
            }
        } else {
            return $category->directory;
        }
    }

    /**
     * @param StaticHtmlCategory $category
     * @return string
     */
    public function getUrlByCategory(StaticHtmlCategory $category) {
        return Util::rewriteUrl('', 'categories').'/'.$this->getDirectoryByCategory($category);
    }

    /**
     * @param $category_id
     * @return array
     */
    public function getAllPostByCategoryId ($category_id) {
        $entry_categories = $this->entry_category->find(array('category_id' => $category_id));

        if ($entry_categories) {
            $posts = array();
            foreach ($entry_categories as $entry) {
                $posts[] = $entry->static_html_entry_id;
            }
        } else {
            $posts = array();
        }

        //全て子の記事を検索
        $child_categories = $this->getAllChildrenOfCategory($category_id);
        foreach ($child_categories as $child_category) {
            $posts = array_unique(array_merge($posts, $this->getAllPostByCategoryId($child_category)));
        }

        return $posts;
    }

    /**
     * @param $category_id
     * @return array
     */
    public function getPostsByCategoryId ($category_id) {
        $entry_categories = $this->entry_category->find(array('category_id' => $category_id));

        if ($entry_categories) {
            $posts = array();
            foreach ($entry_categories as $entry) {
                $posts[] = $entry->static_html_entry_id;
            }
        } else {
            $posts = array();
        }

        return $posts;
    }

    /**
     * @param $entry_id
     * @return aafwEntityContainer|array
     */
    public function getCategoryByEntryId ($entry_id) {
        return $this->entry_category->find(array('static_html_entry_id' => $entry_id));
    }

    /**
     * @param $category_id
     */
    public function deleteStaticHtmlCategorySnsPlugins ($category_id) {
        $sns_plugin = $this->getStaticHtmlCategorySnsPlugins($category_id);
        foreach ($sns_plugin as $plugin) {
            $this->category_sns_plugin->deletePhysical($plugin);
        }
    }

    /**
     * @param $entry_id
     * @param $sns_plugins
     */
    public function createStaticHtmlCategorySnsPlugins ($entry_id, $sns_plugins) {
        foreach ($sns_plugins as $sns_plugin) {
            $sns = $this->category_sns_plugin->createEmptyObject();
            $sns->category_id = $entry_id;
            $sns->sns_plugin_id = $sns_plugin;
            $this->category_sns_plugin->save($sns);
        }
    }

    /**
     * @param $entry_id
     * @return aafwEntityContainer|array
     */
    public function getStaticHtmlCategorySnsPlugins ($entry_id) {
        return $this->category_sns_plugin->find(array('category_id' => $entry_id));
    }

    /**
     * @param $static_html_entry_id
     * @return entity
     */
    public function getStaticHtmlEntryCategoryByStaticHtmlEntryId($static_html_entry_id) {
        return $this->entry_category->findOne(array('static_html_entry_id' => $static_html_entry_id));
    }

    /**
     * @param $static_html_entry_id
     * @return entity|null
     */
    public function getStaticHtmlCategoryByStaticHtmlEntryId($static_html_entry_id) {
        $static_html_entry_category = $this->getStaticHtmlEntryCategoryByStaticHtmlEntryId($static_html_entry_id);
        if ($static_html_entry_category) {
            $static_html_category = $this->getCategoryById($static_html_entry_category->category_id);

            return $static_html_category;
        }

        return null;
    }
}
