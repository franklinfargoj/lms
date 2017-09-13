 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

// BotDetect PHP Captcha configuration options
// more details here: https://captcha.com/doc/php/captcha-options.html
// ----------------------------------------------------------------------------

$config = array(
    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for example page
    |--------------------------------------------------------------------------
    */
    'ExampleCaptcha' => array(
        'UserInputID' => 'CaptchaCode',
        'CodeLength' => 4,
        'CodeStyle' => CodeStyle::Numeric,
        'ImageStyle' => ImageStyle::Overlap2,
        'ImageFormat' => ImageFormat::Png,
        'CustomDarkColor' => '#014481',
        'CustomLightColor' => '#fff',
        'ImageWidth' => 250,
        'ImageHeight' => 50,
    ),

    /*
    |--------------------------------------------------------------------------
    | Captcha configuration for contact page
    |--------------------------------------------------------------------------
    */
    /*'ContactCaptcha' => array(
        'UserInputID' => 'CaptchaCode',
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 6),
        'ImageStyle' => ImageStyle::AncientMosaic,
    ),
*/
    // Add more your Captcha configuration here...
);
