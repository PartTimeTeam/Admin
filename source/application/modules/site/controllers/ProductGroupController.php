<?php
class Site_ProductGroupController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-product' ) );
    }

    public function indexAction() {
    	$productgroup = new ProductGroup();
    	$rs = $productgroup->fetchAllProductGroup();
    	echo "<pre>";
    	print_r($rs);
    	echo "</pre>";
    	exit;
    }
    
}
