<?php
/*
*
*
* Ömer Faruk KASARCIOĞLU
* omr.frk.ksrcglu@gmail.com
*
*
*/
class ModelExtensionModuleXMLExportProduct extends Model {
  
	public function XMLFolder(){
    return DIR_SYSTEM.'../xml/';
	}
	public function XMLFile(){
    return DIR_SYSTEM.'../xml/xml_export_product.xml';
	}
  
	public function getCatalogLink(){
    if(defined('HTTP_CATALOG')){
      return HTTP_CATALOG;
    }else{
      return HTTP_SERVER;
    }
	}
  
	public function getLanguages(){
	  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
    return $query->rows;
	}
  
	public function getProductURL($product_id, $language_id){
    $product_url = str_replace("/admin","",HTTP_SERVER);

	  $query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=".(int)$product_id."' AND language_id = '".(int)$language_id."'");
    if(isset($query->row['keyword']) && $query->row['keyword'] != ''){
      $product_url .= $query->row['keyword'];
    }else{
      $product_url .= "index.php?route=product/product&product_id=".$product_id;
    }
    return $product_url;
	}

	public function getProductDiscount($product_id){
	  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '".(int)$product_id."'");
	  if($query->row){
  	  $date_end      = $query->row['date_end'];
	    $date_end_e    = explode('-',$date_end);  
	    $time_date_end = mktime(0,0,0,$date_end_e[1],$date_end_e[2],$date_end_e[0]);
      if($query->row['date_end'] == '0000-00-00'){
        return $query->row['price'];
      }else{
        if($time_date_end >= time()){return $query->row['price'];}
        else{return false;}
      }
    }
    else{
      return false;
    }
	}
  
	public function getShopName(){
	  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = 'config' AND `key` = 'config_title'");
    if($query->row){return $query->row['value'];}
    else{return '';}
	}
  
	public function getLanguageFolder($lang_id){
	  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE language_id = '".(int)$lang_id."'");
    return $query->row['directory'];
	}
	
	public function getProductName($product_id,$language_id){
	  $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "product_description WHERE product_id = '".(int)$product_id."' AND language_id = '".(int)$language_id."'");
    if($query->row AND $query->row['name'] != ''){return $query->row['name'];}
    else{return '-';}
	}
	
	public function getProductDescription($product_id,$language_id){
	  $query = $this->db->query("SELECT description FROM " . DB_PREFIX . "product_description WHERE product_id = '".(int)$product_id."' AND language_id = '".(int)$language_id."'");
    if($query->row AND $query->row['description'] != ''){return $query->row['description'];}
    else{return '-';}
	}
	
	public function getProductMetaDescription($product_id,$language_id){
	  $query = $this->db->query("SELECT meta_description FROM " . DB_PREFIX . "product_description WHERE product_id = '".(int)$product_id."' AND language_id = '".(int)$language_id."'");
    if($query->row AND $query->row['meta_description'] != ''){return $query->row['meta_description'];}
    else{return '-';}
	}
	
	public function getProductMetaKeyword($product_id,$language_id){
	  $query = $this->db->query("SELECT meta_keyword FROM " . DB_PREFIX . "product_description WHERE product_id = '".(int)$product_id."' AND language_id = '".(int)$language_id."'");
    if($query->row AND $query->row['meta_keyword'] != ''){return $query->row['meta_keyword'];}
    else{return '-';}
	}
	
	public function getProductLengthClass($length_class_id){
	  $query = $this->db->query("SELECT title FROM " . DB_PREFIX . "length_class_description WHERE length_class_id = '".(int)$length_class_id."'");
    if($query->row){return $query->row['title'];}
    else{return '-';}
	}
	
	public function getProductWeightClass($weight_class_id){
	  $query = $this->db->query("SELECT title FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '".(int)$weight_class_id."'");
    if($query->row){return $query->row['title'];}
    else{return '-';}
	}
	
	public function getProductStockStatus($quantity,$stock_status_id){
  	if($quantity == 0){
  	  $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "stock_status WHERE stock_status_id  = '".(int)$stock_status_id."'");
      if($query->row){return $query->row['name'];}
      else{return '-';}
    }else{
      $this->language->load('tool/xml_export');
      return $this->language->get('text_in_stock');
    }
	}
	
	public function getProductManufacturer($manufacturer_id){
    $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id  = '".(int)$manufacturer_id."'");
    if($query->row){return $query->row['name'];}
    else{return '-';}
	}
	
	public function getProductTaxClass($tax_class_id){
	  $query = $this->db->query("SELECT title FROM " . DB_PREFIX . "tax_class WHERE tax_class_id = '".(int)$tax_class_id."'");
    if($query->row){return $query->row['title'];}
    else{return '-';}
	}
	
	public function getPath($category_id){
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		$category_info = $query->row;
		if ($category_info['parent_id']) {
			return $this->getPath($category_info['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $category_info['name'];
		} else {
			return $category_info['name'];
		}
	}

	public function getProductCategory($product_id,$language_id){
    if(version_compare(VERSION, '1.5.0', '>=') AND version_compare(VERSION, '1.5.4.1', '<=')){
  	  $category_return = '';
  	  $splitter        = '>';
  	  $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id  = '".(int)$product_id."' ORDER by category_id ASC");
      foreach($query->rows AS $category){
        $category_path    = $this->getPath($category['category_id'],$language_id);
        $category_return .= '<kategori>'.$category_path.'</kategori>';
      }
    }else{
	  $k = 0;
      $category_return = '';
  	  $splitter        = '>';
  	  $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id  = '".(int)$product_id."' ORDER by category_id ASC");
      foreach($query->rows AS $category){
        $category_id   = $category['category_id'];
        $query         = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR ' ".$splitter." ') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$language_id . "' GROUP BY cp.category_id) AS path, (SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "' AND language_id = '".(int)$language_id."') AS keyword FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$language_id . "'");
        $category_info = $query->row;
        if($category_info){
          $category_path    = ($category_info['path'] ? $category_info['path'] . ' '.$splitter.' ' : '') . htmlspecialchars($category_info['name']);
		  $k++;
          $category_return .= '<kategori'.$k.'><![CDATA['.$category_path.']]></kategori'.$k.'>'."\n";
        }
      }
    }
    return $category_return;
  }

	public function getProductAtribute($product_id,$language_id){
	  $atribute_return = '';
	  $splitter        = '=';
    
    $atribute_return .= '<ATTRIBUTES>'."\n";
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id  = '".(int)$product_id."'");
    foreach($query->rows as $atribute){
      $atribute_group = $this->db->query("SELECT name FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '".(int)$atribute['attribute_id']."' AND language_id = '".(int)$language_id."'");
      $atribute_return .= '<ATTRIBUTE>'."\n";
      $atribute_return .= '<NAME>'.htmlspecialchars($atribute_group->row['name']).'</NAME>'."\n";
      $atribute_return .= '<VALUE>'.$atribute['text'].'</VALUE>'."\n";
      $atribute_return .= '</ATTRIBUTE>'."\n";
    }
    $atribute_return .= '</ATTRIBUTES>'."\n";
  	return $atribute_return;
  }
  
	public function getProductOption($product_id,$language_id){
	  $atribute_return = '';
	  $splitter        = ':';
	
	  
    $query11 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id  = '".(int)$product_id."'");
    foreach($query11->rows as $product_option){
    
      $option_description = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_description WHERE option_id = '".(int)$product_option['option_id']."' AND language_id = '".(int)$language_id."'");
        
  	  $atributes = '';
      $query     = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_id  = '".(int)$product_id."' AND option_id = '".(int)$product_option['option_id']."'");
      foreach($query->rows as $product_option_value){
      
        $option_value_description = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_value_description WHERE option_value_id  = '".(int)$product_option_value['option_value_id']."' AND language_id = '".(int)$language_id."'");
  
        $option_name = "";
        if(isset($option_value_description->row['name'])){
          $option_name = $option_value_description->row['name'];
        }
        
        $atributes  .= '<secenek>'."\n";
	      $atributes  .= '<isim>'.htmlspecialchars($option_name).'</isim>'."\n";
	      $atributes  .= '<fiyat>'.htmlspecialchars($product_option_value['price_prefix'].$product_option_value['price']).'</fiyat>'."\n";
	      $atributes  .= '<adet>'.htmlspecialchars($product_option_value['quantity']).'</adet>'."\n";
	      $atributes  .= '</secenek>'."\n";
      } 
    
    
      $option_description_name = "";
      if(isset($option_description->row['name'])){
        $option_description_name = $option_description->row['name'];
      }
      
      $atribute_return .= '<grup>'."\n";
      $atribute_return .= '<baslik>'.htmlspecialchars($option_description_name).'</baslik>'."\n";
      $atribute_return .= '<secenekler>'.$atributes.'</secenekler>'."\n";
      $atribute_return .= '</grup>'."\n";
    }
  	return $atribute_return;
  }
  
	public function getProductImages($product_id,$full_image_link,$image_number){
	
	  $images_return = '';
    $i = 1;
    
	  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id  = '".(int)$product_id."'");

    if($image_number){
      $images_return .= "\n".'<resim_'.$i.'>';
    }else{
      $images_return .= "\n".'<resim>';
    }
    
    if($full_image_link){
      $images_return .= htmlspecialchars($this->getCatalogLink()."image/".$query->row['image']);
    }else{
      $images_return .= htmlspecialchars($query->row['image']);
    }
    
    if($image_number){
      $images_return .= '</resim_'.$i.'>'."\n";
    }else{
      $images_return .= '</resim>'."\n";
    }
    
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id  = '".(int)$product_id."'");
    foreach($query->rows as $image){
      $i++;
      
      if($image_number){
        $images_return .= "\n".'<resim_'.$i.'>';
      }else{
        $images_return .= "\n".'<resim>';
      }
      
      if($full_image_link){
        $images_return .= htmlspecialchars($this->getCatalogLink()."image/".$image['image']);
      }else{
        $images_return .= htmlspecialchars($image['image']);
      }
      
      if($image_number){
        $images_return .= '</resim_'.$i.'>'."\n";
      }else{
        $images_return .= '</resim>'."\n";
      }
    
    }
  	return $images_return;
  }



	public function exportXML($data){
	  $get_data = array();
	
  	if(isset($data['download']['product_id']) AND $data['download']['product_id'] == true){$get_data[] = 'product_id';}
  	if(isset($data['download']['name']) AND $data['download']['name'] == true){$get_data[] = 'name';}
  	if(isset($data['download']['model']) AND $data['download']['model'] == true){$get_data[] = 'model';}
  	if(isset($data['download']['category']) AND $data['download']['category'] == true){$get_data[] = 'category';}
  	if(isset($data['download']['description']) AND $data['download']['description'] == true){$get_data[] = 'description';}
  	if(isset($data['download']['manufacturer']) AND $data['download']['manufacturer'] == true){$get_data[] = 'manufacturer';}
  	if(isset($data['download']['price']) AND $data['download']['price'] == true){
      $get_data[] = 'price';
  	  $get_data[] = 'special_price';
    }
  	if(isset($data['download']['tax_class']) AND $data['download']['tax_class'] == true){$get_data[] = 'tax_class';}
  	if(isset($data['download']['points']) AND $data['download']['points'] == true){$get_data[] = 'points';}
  	if(isset($data['download']['quantity']) AND $data['download']['quantity'] == true){$get_data[] = 'quantity';}
  	if(isset($data['download']['minimum']) AND $data['download']['minimum'] == true){$get_data[] = 'minimum';}
  	if(isset($data['download']['stock_status']) AND $data['download']['stock_status'] == true){$get_data[] = 'stock_status';}
  	if(isset($data['download']['weight']) AND $data['download']['weight'] == true){$get_data[] = 'weight';}
  	if(isset($data['download']['weight_class']) AND $data['download']['weight_class'] == true){$get_data[] = 'weight_class';}
  	if(isset($data['download']['length']) AND $data['download']['length'] == true){$get_data[] = 'length';}
  	if(isset($data['download']['length_class']) AND $data['download']['length_class'] == true){$get_data[] = 'length_class';}
  	if(isset($data['download']['width']) AND $data['download']['width'] == true){$get_data[] = 'width';}
  	if(isset($data['download']['height']) AND $data['download']['height'] == true){$get_data[] = 'height';}
  	if(isset($data['download']['sku']) AND $data['download']['sku'] == true){$get_data[] = 'sku';}
  	if(isset($data['download']['upc']) AND $data['download']['upc'] == true){$get_data[] = 'upc';}
  	if(isset($data['download']['ean']) AND $data['download']['ean'] == true){$get_data[] = 'ean';}
  	if(isset($data['download']['jan']) AND $data['download']['jan'] == true){$get_data[] = 'jan';}
  	if(isset($data['download']['isbn']) AND $data['download']['isbn'] == true){$get_data[] = 'isbn';}
  	if(isset($data['download']['mpn']) AND $data['download']['mpn'] == true){$get_data[] = 'mpn';}
  	if(isset($data['download']['location']) AND $data['download']['location'] == true){$get_data[] = 'location';}
  	if(isset($data['download']['images']) AND $data['download']['images'] == true){$get_data[] = 'images';}
  	if(isset($data['download']['product_attribute']) AND $data['download']['product_attribute'] == true){$get_data[] = 'product_attribute';}
  	if(isset($data['download']['product_option']) AND $data['download']['product_option'] == true){$get_data[] = 'product_option';}
  	if(isset($data['download']['meta_description']) AND $data['download']['meta_description'] == true){$get_data[] = 'meta_description';}
  	if(isset($data['download']['meta_keyword']) AND $data['download']['meta_keyword'] == true){$get_data[] = 'meta_keyword';}
  	if(isset($data['download']['viewed']) AND $data['download']['viewed'] == true){$get_data[] = 'viewed';}
  	if(isset($data['download']['date_available']) AND $data['download']['date_available'] == true){$get_data[] = 'date_available';}
  	if(isset($data['download']['date_added']) AND $data['download']['date_added'] == true){$get_data[] = 'date_added';}
  	if(isset($data['download']['date_modified']) AND $data['download']['date_modified'] == true){$get_data[] = 'date_modified';}
  	if(isset($data['download']['status']) AND $data['download']['status'] == true){$get_data[] = 'status';}
  	if(isset($data['download']['sort_order']) AND $data['download']['sort_order'] == true){$get_data[] = 'sort_order';}
  	if(isset($data['download']['product_url']) AND $data['download']['product_url'] == true){$get_data[] = 'product_url';}

    $full_image_link = false;
  	if(isset($data['download']['full_image_link'])){$full_image_link = true;}
  	
    $image_number = false;
  	if(isset($data['download']['download_image_number'])){$image_number = true;}

    $export = array();
    $i = 0;
	  $all_products = $this->db->query("SELECT * FROM " . DB_PREFIX . "product");
    foreach($all_products->rows as $product){
      foreach($get_data as $dat){
        if($dat != 'name' AND $dat != 'description' AND $dat != 'meta_description' AND 
           $dat != 'meta_keyword' AND $dat != 'product_attribute' AND $dat != 'product_option' AND 
           $dat != 'length_class' AND $dat != 'weight_class' AND $dat != 'stock_status' AND 
           $dat != 'manufacturer' AND $dat != 'category' AND $dat != 'images' AND 
           $dat != 'tax_class' AND $dat != 'product_url'){
           if($dat == 'price'){
             $export[$i][$dat] = $product[$dat];
           }elseif($dat == 'special_price'){
             $special_price = $this->getProductDiscount($product['product_id']);
             if($special_price){
               $export[$i]['special_price'] = $special_price;
             }else{
               $export[$i]['special_price'] = false;
             }
           }else{
             $export[$i][$dat] = $product[$dat];
           }
        }else{
          if($dat == 'name'){$export[$i][$dat] = $this->getProductName($product['product_id'],$data['language_id']);}
          if($dat == 'description'){$export[$i][$dat] = $this->getProductDescription($product['product_id'],$data['language_id']);}
          if($dat == 'meta_description'){$export[$i][$dat] = $this->getProductMetaDescription($product['product_id'],$data['language_id']);}
          if($dat == 'meta_keyword'){$export[$i][$dat] = $this->getProductMetaKeyword($product['product_id'],$data['language_id']);}
          if($dat == 'length_class'){$export[$i][$dat] = $this->getProductLengthClass($product['length_class_id']);}
          if($dat == 'weight_class'){$export[$i][$dat] = $this->getProductWeightClass($product['weight_class_id']);}
          if($dat == 'stock_status'){$export[$i][$dat] = $this->getProductStockStatus($product['quantity'],$product['stock_status_id']);}
          if($dat == 'manufacturer'){$export[$i][$dat] = $this->getProductManufacturer($product['manufacturer_id']);}
          if($dat == 'category'){$export[$i][$dat] = $this->getProductCategory($product['product_id'],$data['language_id']);}
          if($dat == 'tax_class'){$export[$i][$dat] = $this->getProductTaxClass($product['tax_class_id']);}
          if($dat == 'product_attribute'){$export[$i][$dat] = $this->getProductAtribute($product['product_id'],$data['language_id']);}
          if($dat == 'product_option'){$export[$i][$dat] = $this->getProductOption($product['product_id'],$data['language_id']);}
          if($dat == 'images'){$export[$i][$dat] = $this->getProductImages($product['product_id'],$full_image_link,$image_number);}
          if($dat == 'product_url'){$export[$i][$dat] = $this->getProductURL($product['product_id'],$data['language_id']);}
        }
      }
      $i++;
    }
    
    $return['cols'] = $get_data;
    $return['rows'] = $export;
    $return['language_id'] = $data['language_id'];
    
    if(count($return['cols'])){
      return $this->createXML($return);
    }
    else{return false;}
    
    
	}

	public function createXML($data){
	  $xml = '';
	  $xml .= '<'.'?xml version="1.0" encoding="UTF-8"'.'?'.'>'."\n";
	  $xml .= '<urunler>'."\n";
	  
    foreach($data['rows'] as $row){
    
    
		  $xml .= '<urun>'."\n";
	


        foreach($data['cols'] as $col){
    
          if($col == 'product_id'){$xml .= '<urun_id>'.htmlspecialchars($row['product_id']).'</urun_id>'."\n";}
          if($col == 'model'){$xml .= '<urun_kodu>'.htmlspecialchars($row['model']).'</urun_kodu>'."\n";}
          if($col == 'category'){$xml .= '<kategoriler>'.$row['category'].'</kategoriler>'."\n";}
          if($col == 'name'){$xml .= '<urun_adi><![CDATA['.htmlspecialchars($row['name']).']]></urun_adi>'."\n";}
          if($col == 'description'){$xml .= '<aciklama><![CDATA['.htmlspecialchars($row['description']).']]></aciklama>'."\n";}
          if($col == 'manufacturer'){$xml .= '<marka><![CDATA['.htmlspecialchars($row['manufacturer']).']]></marka>'."\n";}
          if($col == 'price'){$xml .= '<fiyat>'.htmlspecialchars($row['price']).'</fiyat>'."\n";}
          if($col == 'special_price'){$xml .= '<indirimli_fiyat>'.htmlspecialchars($row['special_price']).'</indirimli_fiyat>'."\n";}
          if($col == 'tax_class'){$xml .= '<kdv>'.htmlspecialchars($row['tax_class']).'</kdv>'."\n";}
          if($col == 'points'){$xml .= '<puan>'.htmlspecialchars($row['points']).'</puan>'."\n";}
          if($col == 'quantity'){$xml .= '<adet>'.htmlspecialchars($row['quantity']).'</adet>'."\n";}
          if($col == 'minimum'){$xml .= '<minimum>'.htmlspecialchars($row['minimum']).'</minimum>'."\n";}
          if($col == 'stock_status'){$xml .= '<stok_durumu>'.htmlspecialchars($row['stock_status']).'</stok_durumu>'."\n";}
          if($col == 'weight'){$xml .= '<agirlik>'.htmlspecialchars($row['weight']).'</agirlik>'."\n";}
          if($col == 'weight_class'){$xml .= '<agirlik_grup>'.htmlspecialchars($row['weight_class']).'</agirlik_grup>'."\n";}
          if($col == 'length'){$xml .= '<uzunluk>'.htmlspecialchars($row['length']).'</uzunluk>'."\n";}
          if($col == 'length_class'){$xml .= '<uzunluk_grup>'.htmlspecialchars($row['length_class']).'</uzunluk_grup>'."\n";}
          if($col == 'width'){$xml .= '<genislik>'.htmlspecialchars($row['width']).'</genislik>'."\n";}
          if($col == 'height'){$xml .= '<boy>'.htmlspecialchars($row['height']).'</boy>'."\n";}
          if($col == 'sku'){$xml .= '<sku>'.htmlspecialchars($row['sku']).'</sku>'."\n";}
          if($col == 'upc'){$xml .= '<upc>'.htmlspecialchars($row['upc']).'</upc>'."\n";}
          if($col == 'ean'){$xml .= '<ean>'.htmlspecialchars($row['ean']).'</ean>'."\n";}
          if($col == 'jan'){$xml .= '<jan>'.htmlspecialchars($row['jan']).'</jan>'."\n";}
          if($col == 'isbn'){$xml .= '<isbn>'.htmlspecialchars($row['isbn']).'</isbn>'."\n";}
          if($col == 'mpn'){$xml .= '<mpn>'.htmlspecialchars($row['mpn']).'</mpn>'."\n";}
          if($col == 'location'){$xml .= '<konum>'.htmlspecialchars($row['location']).'</konum>'."\n";}
          if($col == 'images'){$xml .= '<resimler>'.$row['images'].'</resimler>'."\n";}
          if($col == 'product_attribute'){$xml .= '<ozellikler>'.$row['product_attribute'].'</ozellikler>'."\n";}
          if($col == 'product_option'){$xml .= '<urunsecenekleri>'.$row['product_option'].'</urunsecenekleri>'."\n";}
          if($col == 'meta_description'){$xml .= '<meta_aciklama>'.htmlspecialchars($row['meta_description']).'</meta_aciklama>'."\n";}
          if($col == 'meta_keyword'){$xml .= '<meta_kelime>'.htmlspecialchars($row['meta_keyword']).'</meta_kelime>'."\n";}
          if($col == 'viewed'){$xml .= '<goruntuleme>'.htmlspecialchars($row['viewed']).'</goruntuleme>'."\n";}
          if($col == 'date_available'){$xml .= '<gecerlilik>'.htmlspecialchars($row['date_available']).'</gecerlilik>'."\n";}
          if($col == 'date_added'){$xml .= '<ekleme_tarihi>'.htmlspecialchars($row['date_added']).'</ekleme_tarihi>'."\n";}
          if($col == 'date_modified'){$xml .= '<degistirme_tarihi>'.htmlspecialchars($row['date_modified']).'</degistirme_tarihi>'."\n";}
          if($col == 'status'){$xml .= '<durum>'.htmlspecialchars($row['status']).'</durum>'."\n";}
          if($col == 'sort_order'){$xml .= '<siralama>'.htmlspecialchars($row['sort_order']).'</siralama>'."\n";}
          if($col == 'product_url'){$xml .= '<urun_url>'.htmlspecialchars($row['product_url']).'</urun_url>'."\n";}
      }
		  $xml .= '</urun>';
    }
	  $xml .= '</urunler>';
    

  if(!file_exists($this->XMLFolder())){
    mkdir($this->XMLFolder());
  }
  $xml_filename = $this->XMLFile();
  
  if(file_exists($xml_filename)){
    unlink($xml_filename);
  }

  file_put_contents($xml_filename, $xml);
  
  return $xml_filename;
	}

}
?>