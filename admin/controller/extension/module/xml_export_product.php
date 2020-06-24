<?php 
/*
*
*
* Ömer Faruk KASARCIOĞLU
* omr.frk.ksrcglu@gmail.com
*
*
*/
class ControllerExtensionModuleXmlExportProduct extends Controller { 
	private $error = array();
	
	public function index() {
  
		$this->load->language('extension/module/xml_export_product');
		$this->load->model('extension/module/xml_export_product');
		
		if(isset($_GET['alert']) AND $_GET['alert'] == 'success'){
      $data['success'] = $this->language->get('text_success');
    }else{$data['success'] = '';}
		
    
    
    if(isset($_GET['alert']) AND $_GET['alert'] == 'warning'){
      $this->error['warning'] = $this->language->get('text_error_warning');
		}
    
    
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      $xml = $this->model_extension_module_xml_export_product->exportXML($this->request->post);
      if($xml){
        $this->response->redirect($this->url->link('extension/module/xml_export_product', 'user_token=' . $this->session->data['user_token'].'&alert=success&download=true', 'SSL'));
      }else{
        $this->response->redirect($this->url->link('extension/module/xml_export_product', 'user_token=' . $this->session->data['user_token'].'&alert=warning', 'SSL'));
      }
    }
    
		if(isset($_GET['download'])){
      header('Content-disposition: attachment; filename="XML-PRODUCT-EXPORT.xml"');
      header('Content-type: "text/xml"; charset="utf8"');
      readfile(str_replace(DIR_SYSTEM,'',$this->model_extension_module_xml_export_product->XMLFile()));
      die();
    }



		
		
		$this->document->setTitle($this->language->get('heading_title'));
		$data['heading_title']                  = $this->language->get('heading_title');
		$data['text_download_xml']              = $this->language->get('text_download_xml');
		$data['text_export']                    = $this->language->get('text_export');
		$data['text_checked']                   = $this->language->get('text_checked');
		$data['text_check_main_data']           = $this->language->get('text_check_main_data');
		$data['text_check_all']                 = $this->language->get('text_check_all');
		$data['text_check_none']                = $this->language->get('text_check_none');
		$data['text_select_language']           = $this->language->get('text_select_language');
		$data['text_check_all_important_data']  = $this->language->get('text_check_all_important_data');
		$data['text_another_options']           = $this->language->get('text_another_options');
		$data['text_full_image_link']           = $this->language->get('text_full_image_link');
		$data['text_download_image_number']     = $this->language->get('text_download_image_number');
		$data['text_cancel']                    = $this->language->get('text_cancel');
		$data['text_export_items']              = $this->language->get('text_export_items');
		$data['text_export_filter']             = $this->language->get('text_export_filter');
		$data['text_cron_link']                 = $this->language->get('text_cron_link');


		$data['cancel']    = $this->url->link('extension/modification', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cron_link'] = HTTP_CATALOG.'index.php?route=extension/module/xml_export_product';




 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		
		
		
		
		$data['breadcrumbs'] = array();
 		$data['breadcrumbs'][] = array(
   		'text'      => $this->language->get('text_home'),
    	'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),     		
  		'separator' => false
 		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
 		$data['breadcrumbs'][] = array(
   		'text'      => $this->language->get('heading_title'),
	    'href'      => $this->url->link('extension/module/xml_export_product', 'user_token=' . $this->session->data['user_token'], 'SSL'),
  		'separator' => ' :: '
 		);
	
		
		
		$data['languages'] = $this->model_extension_module_xml_export_product->getLanguages();
				
				
				
		$data['items'] = array(
      'product_id'        => $this->language->get('text_product_id'),
      'category'          => $this->language->get('text_category'),
      'stock_status'      => $this->language->get('text_stock_status'),
      'meta_description'  => $this->language->get('text_meta_description'),
      'name'              => $this->language->get('text_name'),
      'manufacturer'      => $this->language->get('text_manufacturer'),
      'weight'            => $this->language->get('text_weight'),
      'meta_keyword'      => $this->language->get('text_meta_keyword'),
      'description'       => $this->language->get('text_description'),
      'price'             => $this->language->get('text_price'),
      'weight_class'      => $this->language->get('text_weight_class'),
      'viewed'            => $this->language->get('text_viewed'),
      'model'             => $this->language->get('text_model'),
      'tax_class'         => $this->language->get('text_tax_class'),
      'length'            => $this->language->get('text_length'),
      'date_available'    => $this->language->get('text_date_available'),
      'sku'               => $this->language->get('text_sku'),
      'product_url'       => $this->language->get('text_product_url'),
      'length_class'      => $this->language->get('text_length_class'),
      'date_modified'     => $this->language->get('text_date_modified'),
      'upc'               => $this->language->get('text_upc'),
      'points'            => $this->language->get('text_points'),
      'width'             => $this->language->get('text_width'),
      'sort_order'        => $this->language->get('text_sort_order'),
      'ean'               => $this->language->get('text_ean'),
      'quantity'          => $this->language->get('text_quantity'),
      'height'            => $this->language->get('text_height'),
      'jan'               => $this->language->get('text_jan'),
      'minimum'           => $this->language->get('text_minimum'),
      'product_attribute' => $this->language->get('text_product_attribute'),
      'location'          => $this->language->get('text_location'),
      'isbn'              => $this->language->get('text_isbn'),
      'product_option'    => $this->language->get('text_product_option'),
      'images'            => $this->language->get('text_images'),
      'mpn'               => $this->language->get('text_mpn'),
      'status'            => $this->language->get('text_status')
    );
  






    
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['actual_url'] = $_SERVER['REQUEST_URI'];
		$data['year'] = date("Y");

		$this->response->setOutput($this->load->view('extension/module/xml_export_product', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/xml_export_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
?>