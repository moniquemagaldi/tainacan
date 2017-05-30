<?php

ini_set('max_input_vars', '10000');
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '/helpers_api_model.php');

class SynchronizeModel extends Model { 
    public  $User;
    public $Pass;
    public $url;
    public $token;
    public $rootCategory = false;
    public $metasCollection;
    public $rootCategoryCollection = [];
    // todos os metadados
    public $collectionProperties;
    
    /***************************** Metodos para leitura na API ****************/
    
    public function readCollectionsPublished(){
        $qry_str = "/posts?type=socialdb_collection&filter[status]=publish&user=".$this->User.'&password='.$this->Pass;;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->url. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return json_decode($content, true);
    }
    
    public function readCollectionMeta($id){
        $qry_str = "/posts/" . $id . "/meta?user=".$this->User.'&password='.$this->Pass;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL,$this->url. $qry_str);
        //curl_setopt($ch, CURLOPT_USERPWD, "$this->User:$this->Pass"); //Your credentials goes here
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $metas = trim(curl_exec($ch));
        $metas_collection = json_decode($metas, true);
        return $metas_collection;
    }
    
    public function readTerm($id) {
        $qry_str = "/taxonomies/socialdb_category_type/terms/".$id."?user=".$this->User.'&password='.$this->Pass;;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->url. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return json_decode($content, true);
    }
    
    public function readProperty($id) {
        $qry_str = "/taxonomies/socialdb_property_type/terms/".$id."?user=".$this->User.'&password='.$this->Pass;;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->url. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return json_decode($content, true);
    }
    
    public function readTermMetas($id) {
        $qry_str = "/taxonomies/socialdb_category_type/terms/".$id."/meta?user=".$this->User.'&password='.$this->Pass;;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->url. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return json_decode($content, true);
    }
    
    public function readPropertyMetas($id) {
        $qry_str = "/taxonomies/socialdb_property_type/terms/".$id."/meta?user=".$this->User.'&password='.$this->Pass;;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->url. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return json_decode($content, true);
    }
    
    public function readCategoriesChildren($term_id) {
        $qry_str = "/taxonomies/socialdb_category_type/terms?filter[child_of]=".$term_id."&user=".$this->User.'&password='.$this->Pass;;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->url. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        return json_decode($content, true);
    }
    
    /***************************************************************************/
    
   
    /**
     *  Metodo de inicializacao da API
     * @param type $data
     */
    public function start($data) {
        $this->User = $data['api_user'];
        $this->Pass = $data['api_key'];
        $this->url = $data['api_url'];
        $this->token = md5(uniqid(rand(), true));
        $content = $this->readCollectionsPublished();
        if (empty($content)) {
            return false;
        } else {
            foreach ($content as $post) {
                if(!isset($post['ID']))
                    continue;
                $this->collectionProperties = [];
                $this->metasCollection = $this->readCollectionMeta($post['ID']);
                //setando a variavel da classe com a categoria raiz
                $this->setCategoryRootCollection();
                //insere a categoira e desce recursivamente as propriedades do termo raiz
                $this->getProperties($this->rootCategoryCollection);
                //desce as categorias filha da categoria
                $this->findChildrenCategoriesAndProperties($this->rootCategoryCollection);
                //sobe atraves da categoria raiz se exisitr uma hierarquia
                $this->getPropertiesAbove($this->rootCategoryCollection) ;
                //
                $this->getPropertiesAbove($this->rootCategoryCollection) ;
                //atualizando as colecoes
                $this->updateCollection($post,$metas);
                
                
                
            }
        }  
    }
    
    /**
     * 
     * @param type $post
     * @param type $metas
     */
    public function updateCollection($post,$metas) {
        if(!MappingAPI::hasMapping($this->url, 'collections', $post['ID'])){
            $has_post_with_this_name = get_post_by_name($post['title']);
            if($has_post_with_this_name){
                MappingAPI::saveMapping($this->url, 'collections', $post['ID'], $has_post_with_this_name->ID);
                HelpersAPIModel::updateCollection($post,$metas,$has_post_with_this_name->ID);
            }else{
                $id = HelpersAPIModel::createCollection($post);
                MappingAPI::saveMapping($this->url, 'collections', $post['ID'],$id);
            }
        }else{
            $ID = MappingAPI::hasMapping($this->url, 'collections', $post['ID']);
            HelpersAPIModel::updateCollection($post,$metas,$ID);
        }
    }
    
    /**
     * 
     * @param type $category
     */
    public function updateCategory($category,$metas){
        if(!MappingAPI::hasMapping($this->url, 'categories', $category['ID'])){
            if(isset($category['parent']) && $category['parent']['slug'] == 'socialdb_taxonomy'){
                $id = HelpersAPIModel::createCategory($category,$metas, get_term_by('slug', 'socialdb_taxonomy', 'socialdb_category_type')->term_id);
            }else if(isset($category['parent']) && $category['parent']['slug'] == 'socialdb_category'){
                $id = HelpersAPIModel::createCategory($category,$metas,get_term_by('slug', 'socialdb_category', 'socialdb_category_type')->term_id);
            }else if(isset($category['parent'])){
                if(!MappingAPI::hasMapping($this->url, 'categories',$category['parent']['ID'])){
                    $id = HelpersAPIModel::createCategory($category,$metas);
                }else{
                    $ID_parent = MappingAPI::hasMapping($this->url, 'categories', $category['parent']['ID']);
                    $id = HelpersAPIModel::createCategory($category,$metas,$ID_parent);
                }
            }
            MappingAPI::saveMapping($this->url, 'categories', $category['ID'],$id);
        }else{
            $ID = MappingAPI::hasMapping($this->url, 'categories', $category['ID']);
            HelpersAPIModel::updateCategory($ID,$category,$metas);
        }
    }
    
    /*
     * seta a categoria raiz da colecao attual
     */
    public function setCategoryRootCollection() {
        if(is_array($this->metasCollection)){
            foreach ($this->metasCollection as $meta) {
                if($meta['key'] === 'socialdb_collection_object_type'){
                        $this->rootCategoryCollection = $this->readTerm($meta['value']);
                }
            }
        }
    }
    
    
     public function getPropertiesAbove($term) {
        if($term['parent']){
            $metas = $this->readTermMetas($term['parent']['ID']);
            if($term['parent']['slug'] == 'socialdb_category' && !$this->rootCategory){
                $this->rootCategory = TRUE;
                $metas = $this->readTermMetas($term['parent']['ID']);
                if(is_array($metas)){
                    foreach ($metas as $meta) {
                        if($meta['key'] == 'socialdb_category_property_id'){
                            $property_metas = $this->readPropertyMetas($meta['value']);
                            $this->collectionProperties[$term['parent']['ID']][$meta['value']] = $property_metas;
                            $this->findCategoriesInProperties($property_metas);
                        }
                    }
                }
            }else if($term['parent']['slug'] !== 'socialdb_category' && $term['parent']['slug'] !== 'socialdb_taxonomy'){
                $newTem = $this->readTerm($term['parent']['ID']);
                $this->getProperties($term['parent']);
                $this->getPropertiesAbove($newTem);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    
    
    /**
     * busca os metadados do termo 
     * @param type $term
     * @return boolean
     */
    public function getProperties($term) {
        if($term['ID']){
            $metas = $this->readTermMetas($term['ID']);
            $this->updateCategory($term,$metas);
            if(is_array($metas)){
                foreach ($metas as $meta) {
                    if($meta['key'] == 'socialdb_category_property_id'){
                        $property_metas = $this->readPropertyMetas($meta['value']);
                        //adiciono os metas encontrados no array de dados de metadados
                        $this->collectionProperties[$term['ID']][$meta['value']] = $property_metas;
                        $this->findCategoriesInProperties($property_metas);
                    }
                }
            }
        }
    }
    
    /**
     * olha dentro dos metadados as categorias que deve3m ser procuradas
     * @param type $metas
     */
    public function findCategoriesInProperties($metas) {
        if($metas && is_array($metas)){
            foreach ($metas as $meta) {
                if(($meta['key'] == 'socialdb_property_term_root' || $meta['key'] == 'socialdb_property_object_category_id') && trim($meta['value']) != ''){
                    $ids = explode(',', $meta['value']);
                    foreach ($ids as $id) {
                        $term = $this->readTerm($id);// le o termo que estava dentro do meta deste metadado
                        $this->getProperties($term);//busca seus metadados e ja salvo sua categoira
                        $this->findChildrenCategoriesAndProperties($term);// verifico se a categoria possui filhos
                    }
                }
            }
        }
    }

    /**
     * Percorro os filhos para atualizar a categoria e veridcar se seus metadados possuem categorias
     * @param type $term
     */
    public function findChildrenCategoriesAndProperties($term) {
        $children = $this->readCategoriesChildren($term['ID']);
        if($children && is_array($children)){
            foreach ($children as $child) {
                $this->getProperties($child);
            }
        }
    }
    
    
    public function processProperties($param) {
        
    }
}

################################################################################
class MappingAPI extends Model{
    public static function hasMapping($url,$type,$id_api){
        $option = get_option('mapping-api-tainacan');
        if($option){
           $array = unserialize($option); 
           foreach ($array as $index => $map) {
               if($map['url'] == $url && isset($map[$type][$id_api])){
                   return $map[$type][$id_api];
               }
           }
           //se nao foi mapeado
           return false;
        }else{
           return false;
        }
    }
    
    
    /**
     * 
     * @param type $url
     * @param type $type
     * @param type $id_api
     * @param type $id_blog
     */
    public static function saveMapping($url,$type,$id_api,$id_blog){
        $block = false;
        $option = get_option('mapping-api-tainacan');
        if($option){
           $array = unserialize($option); 
           foreach ($array as $index => $map) {
               if($map['url'] == $url){
                   $block = TRUE;
                   $map[$type][$id_api] = $id_blog;
               }
               $array[$index] = $map;
           }
           //se nao foi mapeado
           if(!$block){
                $var = array('url'=>$url,'collections'=>[],'properties'=>[],'categories'=>[]);
                $var[$type][$id_api] = $id_blog;
                $array[] = $var; 
           }
           update_option('mapping-api-tainacan', serialize($array));
        }else{
            $var = array('url'=>$url,'collections'=>[],'properties'=>[],'categories'=>[]);
            $var[$type][$id_api] = $id_blog;
            update_option('mapping-api-tainacan', serialize([$var]));
        }
    }
}