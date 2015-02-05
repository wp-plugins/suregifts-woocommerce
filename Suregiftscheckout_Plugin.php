<?php


include_once('Suregiftscheckout_LifeCycle.php');

class Suregiftscheckout_Plugin extends Suregiftscheckout_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'UsernameInput' => array(__('Username', 'suregiftscheckout-plugin')),
            'PasswordInput' => array(__('Password', 'suregiftscheckout-plugin')),
            'WebsiteHostInput' => array(__('WebsiteHost', 'suregiftscheckout-plugin')),
            'MessageInput' => array(__('Store Message', 'suregiftscheckout-plugin')),
            'TestMode' => array(__('Test Mode', 'suregiftscheckout-plugin'), 'true', 'false'),
             /*'Donated' => array(__('I have donated to this plugin', 'my-awesome-plugin'), 'false', 'true'),
           'CanSeeSubmitData' => array(__('Can See Submission data', 'my-awesome-plugin'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone')*/
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        //echo //($options); 
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }


    }


    public function getPluginDisplayName() {
        return 'suregiftscheckout';
    }

    protected function getMainPluginFileName() {
        return 'suregiftscheckout.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

   public function addActionsAndFilters() {

       
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
        add_filter( 'woocommerce_checkout_coupon_message', array(&$this,'woocommerce_rename_coupon_message_on_checkout' ));
        //add_action( 'woocommerce_before_cart_table', array(&$this, 'print_notice' ));
        add_action( 'woocommerce_get_shop_coupon_data', array(&$this, 'suregifts_process_valid_coupon' ));

        add_action('woocommerce_after_cart_contents',array(&$this, 'suregifts_woocommerce_after_cart_table'));
        add_action( 'woocommerce_cart_calculate_fees', array(&$this, 'suregifts_add_cart_fee') );
        add_action( 'woocommerce_after_checkout_validation', array(&$this, 'suregifts_checkout_validation') );
       


    }
 


// rename the "Have a Coupon?" message on the checkout page
function woocommerce_rename_coupon_message_on_checkout() {

    return 'Have a Coupon or giftcard code from <a href="http://www.suregifts.com.ng" target="_blank">Suregifts</a>?';
}


// rename the coupon field on the checkout page
function woocommerce_rename_coupon_field_on_checkout( $translated_text, $text, $text_domain ) {

    // bail if not modifying frontend woocommerce text
    if ( is_admin() || 'woocommerce' !== $text_domain ) {
        return $translated_text;
    }

    if ( 'Coupon code' === $text ) {
        $translated_text = 'Apply Coupon or Suregifts Coupon';
    
    } elseif ( 'Apply Coupon' === $text ) {
        $translated_text = 'Apply Coupon or Suregifts Coupon ';
    }

    return $translated_text;
}



  // function print_notice(){
  //   global $woocommerce;
  //          //$message ="we accept suregifts coupon codes";
  //          $msg =$this->getOption('MessageInput');

  //         if (!empty($msg )){
  //          $message= $msg.'  Use your Coupon/<a href="http://www.suregifts.com.ng" target="_blank">Suregifts</a> Voucher Below';
          
           
  //         }else{
  //            $message= 'enter your <a href="http://www.suregifts.com.ng" target="_blank">Suregifts.com</a> giftcard code';
  //         }
  //          wc_print_notice( $message, $notice_type = 'notice' );

  //       }



function  suregifts_woocommerce_after_cart_table(){
    global $woocommerce;
    echo '<div class="submit">';
             // echo '<h3>SureGifts GiftCard</h3>';
              
                 if (!$woocommerce->session->suregiftcard) {
              //      echo '<input type="text" name="suregift_card" />';
              // echo '  <input type="submit" name="suregift_card-btn" value="Apply"/>';

              echo '
              <table cellspacing="0"><tbody><tr><td colspan="6" class="actions">
              <div class="coupon">
              <label style="display:block" for="coupon_code">Suregifts GiftCard:</label> 
              <input type="text"  name="suregift_card" class="input-text"  value="" placeholder="GiftCard code">
               <input type="submit" class="button" name="suregift_card-btn" value="Apply giftcard"></div></td></tr></tbody><table>';
                 }else {
                  // echo ' <input type="submit" name="store-wallet-btn" value="Apply"/>';
                  //echo '<input type="text" name="suregift_card" readonly value="giftcard code: '.$woocommerce->session->suregiftcard.'" /><input type="submit" name="un-suregift_card-btn" value="Remove"/>';
                //echo '<input type="submit" name="un-suregift_card-btn" value="Remove"/>';
                    echo '
              <table cellspacing="0"><tbody><tr><td colspan="6" class="actions">
              <div class="coupon">
              <label style="display:block" for="coupon_code">Suregifts GiftCard:</label> 
              
              <input type="text" style="width:50%" name="suregift_card" readonly  value="'.$woocommerce->session->suregiftcard.'">
              <input type="submit" class="button" name="un-suregift_card-btn" value="Remove"/></div></td></tr></tbody><table>';

                 }
              echo '</div>';
    }




    function suregifts_add_cart_fee() {
      
      global $woocommerce;
      if(isset($_POST['suregift_card-btn']) ){
      // $username =$this->merchant_username;
      // $password =$this->merchant_password;
      // $websitehost =$this -> merchant_webhost;
      // $mode =$this->testmode;
        $username =$this->getOption('UsernameInput');
    $password =$this->getOption('PasswordInput');
    $websitehost = $this-> getOption('WebsiteHostInput');
    $mode =$this->getOption('TestMode');
      $coupon_code = $_POST['suregift_card'];
      $auth = $username.':'.$password;

    if ($mode == "true"){
      $ch = curl_init("https://stagging.oms-suregifts.com/api/voucher/?vouchercode=".$coupon_code); 
      }else{
         $ch = curl_init("https://oms-suregifts.com/api/voucher/?vouchercode=".$coupon_code); 
      }

          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  "Authorization: Basic ".base64_encode($auth),
                    )
              );

          $response = curl_exec($ch);

          $returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
          curl_close($ch);
      

          $res = json_decode($response, true);
        //if ( 'yes' == $this->debug )
      // $this->log->add( 'suregifts_giftcardapi', 'Response for GET card id '.$coupon_code.'==>' .$response );     
        if ($res['AmountToUse']>0){
        $woocommerce->session->use_suregiftcard = true;
        $woocommerce->session->suregiftcard_amt =$res['AmountToUse'];
         $woocommerce->session->suregiftcard=$coupon_code;
      
    wc_add_notice( 'Your SureGifts Card has been applied successfully', 'success' );
    //$woocommerce->add_notice(__('Your SureGifts Card has been applied successfully', 'woocommerce-suregifts-giftcardapi'));
      }else{
        //if (isset($_POST['store-wallet-btn'])){
      $woocommerce->add_error(__('The SureGift card has been used or invalid', 'woocommerce-suregifts-giftcardapi'));
        //}
                 }
         }

        if(isset($_POST['un-suregift_card-btn'])){
            unset($woocommerce->session->use_suregiftcard);
          unset($woocommerce->session->suregiftcard);
          unset($woocommerce->session->suregiftcard_amt);
          
           
          
                 }
      if ($woocommerce->session->suregiftcard_amt){
      $woocommerce->cart->add_fee( __('SureGifts Card: '.$woocommerce->session->suregiftcard, 'woocommerce'), -$woocommerce->session->suregiftcard_amt);
      }
                
      }




    function suregifts_checkout_validation($posted){
      global $woocommerce;
      $websitehost = $this-> getOption('WebsiteHostInput');
        $data = array( 
                "AmountToUse" => $woocommerce->session->suregiftcard_amt, 
                "VoucherCode" => $woocommerce->session->suregiftcard,
                "WebsiteHost" => $websitehost
              );  

        $data_string = json_encode($data);       
      $mode =$this->testmode;                                                                            
        
        if ($mode == "true"){
          $ch = curl_init('https://stagging.oms-suregifts.com/api/voucher');
          }else{
           $ch = curl_init('https://oms-suregifts.com/api/voucher');
        }
                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        //,
            'Content-Length: ' . strlen($data_string),
            "Authorization: Basic ".base64_encode($auth),
             )
          );
        $result = curl_exec($ch);
        $coupon_res = json_decode($result, true);

        $coupon_res_code = $coupon_res['Response'];
    $desc=$coupon_res['Description'];
    
      // if ( 'yes' == $this->debug )
      // $this->log->add( 'suregifts_giftcardapi', 'Response for POST card id '.$woocommerce->session->suregiftcard.'==>' . $result );


        //die($coupon_res_code);
          if ($coupon_res_code != "00"){
      $woocommerce->add_error(__(($desc!=null?$desc:"Unable to POST your SureGiftsCard"), 'woocommerce-suregifts-giftcardapi'));
      
      
      }else{
        unset($woocommerce->session->use_suregiftcard);
        unset($woocommerce->session->suregiftcard);
        unset($woocommerce->session->suregiftcard_amt);
      
    }
  
}



}



  
  
