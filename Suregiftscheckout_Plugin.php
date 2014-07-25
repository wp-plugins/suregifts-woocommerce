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

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
        add_filter( 'woocommerce_checkout_coupon_message', array(&$this,'woocommerce_rename_coupon_message_on_checkout' ));
        //add_filter( 'gettext', array(&$this, 'woocommerce_rename_coupon_field_on_checkout'), 10, 3 );
       // add_action ('woocommerce_applied_coupon', array(&$this,'suregifts_process_coupon')); 
        //add_action( 'woocommerce_coupon_error', array(&$this, 'suregifts_process_invalid_shop_coupon' ));
        add_action( 'woocommerce_before_cart_table', array(&$this, 'print_notice' ));
        add_action( 'woocommerce_get_shop_coupon_data', array(&$this, 'suregifts_process_valid_coupon' ));
       


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



  function print_notice(){
    global $woocommerce;
           //$message ="we accept suregifts coupon codes";
           $msg =$this->getOption('MessageInput');

          if (!empty($msg )){
           $message= $msg.'  Use your Coupon/<a href="http://www.suregifts.com.ng" target="_blank">Suregifts</a> Voucher Below';
          
           
          }else{
             $message= 'enter your <a href="http://www.suregifts.com.ng" target="_blank">Suregifts.com</a> giftcard code';
          }
           wc_print_notice( $message, $notice_type = 'notice' );

        }

  function suregifts_process_valid_coupon(){

    global $woocommerce;
    $username =$this->getOption('UsernameInput');
    $password =$this->getOption('PasswordInput');
    $mode =$this->getOption('TestMode');
    $coupon_code = $_POST['coupon_code'];
    //$username = 'Booksville';
    //$password = 'AMekd7/vjQYePvWf5E7j0lPRkGL3Oc30LS3blRcpprCyy2QO9mZecE07vTaBIK3rDA==';
    $auth = $username.':'.$password;
    //die($auth);
    //$vouchercode = '48426939';
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

          if ($res['AmountToUse'] != 0){

            $data = array( 
                "AmountToUse" => $res['AmountToUse'] , 
                "VoucherCode" => $coupon_code,
                "WebsiteHost" => ''
              );  

        $data_string = json_encode($data);                                                                                   
        
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
        //die($coupon_res_code);
          if ($coupon_res_code == "00"){

               $amount = $res['AmountToUse'] ; // Amount
                //$amount = 3000;
                $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product
                $usage_limit = 1; 
                $description = 'Suregifts gift card';
                              
                $coupon = array(
                    'post_title' => $coupon_code,
                    'post_content' => 'suregifts giftcard',
                    'post_excerpt' => 'suregifts giftcard',
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_type'     => 'shop_coupon',
                    
                );
                                    
                $new_coupon_id = wp_insert_post( $coupon );
                                    
                // Add meta
                update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
                update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
                update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
                update_post_meta( $new_coupon_id, 'product_ids', '' );
                update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
                update_post_meta( $new_coupon_id, 'usage_limit', 1 );
                update_post_meta( $new_coupon_id, 'expiry_date', '' );
                update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
                update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
                //update_post_meta( $new_coupon_id, 'usage_limit_per_user', 1 );
               // update_post_meta( $new_coupon_id, 'excerpt', $description );

          }
        }

  }

}



  
  
