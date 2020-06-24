<?php
/*
*
*
* Ömer Faruk KASARCIOĞLU
* omr.frk.ksrcglu@gmail.com
*
*
*/
class ControllerExtensionModuleXMLExportProduct extends Controller {
	public function index(){

$export_data            = array('download' => array(


'product_id'            => false,
'model'                 => true,
'category'              => true,
'name'                  => true,
'description'           => true,
'manufacturer'          => true,
'price'                 => true,
'tax_class'             => true,
'points'                => false,
'quantity'              => true,
'minimum'               => false,
'stock_status'          => false,
'weight'                => false,
'weight_class'          => false,
'length'                => false,
'length_class'          => false,
'width'                 => false,
'height'                => false,
'sku'                   => false,
'upc'                   => false,
'location'              => false,
'images'                => true,
'product_attribute'     => false,
'product_option'        => true,
'meta_description'      => false,
'meta_keyword'          => false,
'viewed'                => false,
'date_available'        => false,
'date_added'            => false,
'date_modified'         => false,
'status'                => true,
'sort_order'            => false,
'product_url'           => false,
'meta_description'      => false,
'full_image_link'       => true,
'download_image_number' => true,
),

'language_id'           => 1,         
'language_folder'       => 'tr-tr',   
'show_xml'              => true,   

);


/******************************************************************************/


  $registry = new Registry();

  $config = new Config();
  $config->load('admin');

  $loader = new Loader($registry);
  $registry->set('load', $loader);


  $language = new Language($config->get('language_directory'));
  $registry->set('language', $language);


  if ($config->has('language_autoload')) {
  	foreach ($config->get('language_autoload') as $value) {
  		$loader->language($value);
  	}
  }


  if ($config->get('db_autostart')) {
  	$registry->set('db', new DB($config->get('db_engine'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port')));
  }
  
require_once('admin/model/extension/module/xml_export_product.php');

$exportToXML = new ModelExtensionModuleXMLExportProduct($registry);
$exportToXML->exportXML($export_data);



if($export_data['show_xml']){
  header("Content-type: text/xml");
  $xml = file_get_contents($exportToXML->XMLFile());
  echo $xml;
  die();
}else{
  echo 'DONE';
}




  }
}