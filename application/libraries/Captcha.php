<?php

class Captcha {

    private $vals = array();
    private $baseUrl;
    private $basePath;
    private $captchaImagePath;
    private $captchaImageUrl;
    private $captchaFontPath;

    public function __construct($configVal = array()) {
        $this->CI_OBJ = & get_instance();
        $this->baseUrl = $this->CI_OBJ->config->item('base_url');
        // echo $this->baseUrl;
        // die;
        $this->basePath = $this->CI_OBJ->input->server('DOCUMENT_ROOT') . '/';
        //$this->captchaFontPath = 'monofont.ttf';
        $this->captchaFontPath 	 = $this->baseUrl.'/'.ASSETS.'fonts/Lato-Regular.woff';
    //    echo $this->CI_OBJ->input->server('DOCUMENT_ROOT');die;
        //$chars = "abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $chars = "0123456789";
        /* echo $this->captchaFontPath;
          echo file_exists('monofont.ttf');
          die; */
        srand((double) microtime() * 1000000);

        $i = 0;

        $pass = '';

        while ($i <= 7) {

            $num = rand() % 33;

            $tmp = substr($chars, $num, 1);

            $pass = $pass . $tmp;
            //  echo $tmp . " - " . $pass;
            $i++;
        }

        $randomString = "";
        for ($i = 0; $i < 4; $i++) {
            $randomString .= $chars[rand(0, strlen($chars) - 1)];
        }

        $this->CI_OBJ->load->helper('captcha');
        //echo $pass;
        if (!empty($config))
            $this->initialize($configVal);
        else
            $this->vals = array(
                'word' => $randomString,
                'word_length' => '4',
                'img_path' => 'captcha/',
                'img_url' => $this->baseUrl . '/captcha/',
                'font_path' => $this->captchaFontPath,
                'img_width' => '240',
                'img_height' => '30',
                'expiration' => 3600,
                'font_size' => '50',
                'colors'        => array(
                    'background' => array(255, 255, 255),
                    'border' => array(255, 255, 255),
                    'text' => array(0, 0, 0),
                    'grid' => ''
                )
            );
        //print_r($this->vals);
    }

    /**
     * initializes the variables
     *
     * @author    Mohammad Jahedur Rahman
     * @access    public
     * @param     array     config
     */
    public function initialize($configVal = array()) {
        $this->vals = $configVal;
    }

//end function initialize
    //---------------------------------------------------------------

    /**
     * generate the captcha
     *
     * @author     Mohammad Jahedur Rahman
     * @access     public
     * @return     array
     */
    public function generateCaptcha($img_ht = "") {
        if ($img_ht != "")
            $this->vals['img_height'] = $img_ht;
        // print_r($this->vals);
        // die;
        $cap = create_captcha($this->vals);

        return $cap;
    }

//end function generateCaptcha
}

?>
