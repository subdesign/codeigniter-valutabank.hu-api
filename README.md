# Valutabank.hu API library for Codeigniter

Simple library for getting currency data from valutabank.hu  
The exchange value is in HUF (Hungarian Forint)

***

## Setup

Edit the __valutabank.php__ _config_ file.

    $config['currencies'] = array('USD', 'EUR', 'CHF');  // array of currencies or the string "all" if you want all
    $config['returntype'] = 'html';  // html OR array
    $config['icon_path']  = 'assets/images/';  // relative path to the icon images
    $config['icon_name']  = 'icon';   // icon name prefix. it will be "icon-usd", "icon-eur" etc.
    $config['icon_ext']   = 'jpg';	 // extension of icon image files

Or pass an array of parameters to the library:

    $params = array(
        'currencies' => 'all',
        'returntype' => 'array',
        'icon_path'  => 'assets/images/',
        'icon_name'  => 'icon',
        'icon_ext'   => 'png'
    );

    $this->load->library('valutabank', $params);

## Usage

Load the library

    $this->load->library('valutabank');

Use the arfolyamok() method to get the exchange rates.

    $result = $this->valutabank->arfolyamok();

Example array result:

    array(5) {  
        ["last_update"]=>  
        string(19) "2012-11-26 10:17:16"  
        ["CHF"]=>  
        array(3) {  
            ["veteli"]=>  
            string(6) "229.01"  
            ["eladasi"]=>  
            string(6) "236.98"  
            ["ikon"]=>  
            string(54) "http://localhost/valutabank/assets/images/icon-chf.jpg"  
        }  
    ...

## Caching

It's recommended to use some kind of caching to spare the source server.

## License 

[MIT License](http://www.opensource.org/licenses/MIT)

## Author

C. 2012 Barna Szalai <b.sz@devartpro.com>

Feel free to contant me if you have any questions!



