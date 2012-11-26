<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Valutabank API library
 *
 * @author Barna Szalai <b.sz@devartpro.com>
 * @license http://www.opensource.org/licenses/MIT  MIT License
 * @link https://github.com/subdesign/codeigniter-valutabank.hu-api
 * @version 1.0.0
 */

class Valutabank
{
	/**
	* The CodeIgniter instance.
	*
	* @var object
	* @access private
	*/
	private $_CI;

	/**
	* Currencies array/string
	*
	* @var mutiple
	* @access private
	*/
	private $_currencies;

	/**
	* Returntype of result
	*
	* @var multiple
	* @access private
	*/
	private $_returntype;

	/**
	* API source address
	*
	* @var string
	* @access private
	*/
	private $_source_url = 'http://www.valutabank.hu/rss/valutabank.xml';

	/**
	* Path of currency icon
	*
	* @var object
	* @access private
	*/
	private $_icon_path;

	/**
	* Prefix name of currency icon
	*
	* @var object
	* @access private
	*/
	private $_icon_name;

	/**
	* Extension of currency icon
	*
	* @var object
	* @access private
	*/
	private $_icon_ext;

	/**
	 * Constructor
	 * 
	 * Gets the CI instance and runs initialization method.
	 * 
	 * @param array $config Config options
	 * @return  void
	 */
	public function __construct($config = array())
	{
		$this->_CI =& get_instance();
		
		if( ! empty($config))
		{
			$this->_initialize($config);			
		}
		else
		{
			$config = $this->_CI->config->load('valutabank');	
			$this->_initialize($config);
		}		
	}

	/**
	 * Initialize class
	 * 
	 * @param array $config Config settings
	 * @return void
	 * @access private
	 */
	private function _initialize($config = array())
	{
		if( ! is_array($config))
		{
			throw new Exception("Config variable must be an array");			
		}

		foreach($config as $k => $v)
		{
			$this->{'_'.$k} = $v;
		}
	}

	/**
	 * Main method for getting and returning currency datas
	 * 
	 * @return void
	 * @access public
	 */
	public function arfolyamok()	
	{
		$cont = file_get_contents($this->_source_url);
		
		$xml = simplexml_load_string($cont, 'SimpleXMLElement', LIBXML_NOCDATA);
			
		$last_update = date("Y-m-d H:i:s", strtotime($xml->channel->pubDate));

		if($this->_returntype == 'html')
		{
			$result = $this->_render_html($xml->channel->item, $last_update);
		}
		elseif($this->_returntype == 'array')
		{
			$result = $this->_render_array($xml->channel->item, $last_update);	
		}
		else
		{
			throw new Exception("Error in config settings, return type isn't set correctly.");			
		}

		return $result;
	}

	/**
	 * Submethod for rendering html result
	 * 
	 * @param  array $data        Data for building html
	 * @param  string $last_update Date of last update
	 * @return  string
	 * @access private
	 */
	private function _render_html($data, $last_update)	
	{
		$html = '';
		$html .= '<table align="center" class="exchange"><thead><th></th><th>Vételi</th><th>Eladási</th></thead><tbody>';

		if(is_string($this->_currencies) && $this->_currencies === 'all')
		{
			foreach($data as $key => $value)
			{			
				preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

				$html .= '<tr>';
				$html .='<td align="left" width="90">'.substr($value->title, 0, 3).'&nbsp;<img src="'.base_url().$this->_icon_path.$this->_icon_name.'-'.strtolower(substr($value->title, 0, 3)).'.'.$this->_icon_ext.'" border="0"/>'.nbs(1).'</td><td align="right">'.$matches[0][0].' Ft&nbsp;&nbsp;</td><td align="right">'.$matches[0][1].' Ft</td>';
				$html .= '</tr>';
			}
		}
		elseif(is_array($this->_currencies) && count($this->_currencies) > 0)
		{
			foreach($data as $key => $value)
			{			
				foreach($this->_currencies as $c)
				{				
					if(substr($value->title, 0, 3) == $c)
					{						
						preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

						$html .= '<tr>';
						$html .='<td align="left" width="90">'.$c.'&nbsp;<img src="'.base_url().$this->_icon_path.$this->_icon_name.'-'.strtolower($c).'.'.$this->_icon_ext.'" border="0"/>'.nbs(1).'</td><td align="right">'.$matches[0][0].' Ft&nbsp;&nbsp;</td><td align="right">'.$matches[0][1].' Ft</td>';
						$html .= '</tr>';

						// optimize performance
						$this->_currencies = array_diff($this->_currencies, array($c));
					}
				}

				// if currencies run out, don't continue foreach loop
				if( ! count($this->_currencies)) break;				
			}
		}
		else
		{
			throw new Exception("Error in config settings, currencies aren't set correctly.");			
		}

		$html .= '<tr><td colspan="3" align="center"><span class="exc-date">Utolsó változás '.$last_update.'<br/><span style="font-size:9px;">Forrás: valutabank.hu</span></span></td></tr>';
		$html .= '</tbody></table>';

		return $html;
	}

	/**
	 * Submethod for rendering array result
	 * 
	 * @param  array $data        Data for building array result
	 * @param  string $last_update Date of last update
	 * @return  array
	 * @access private
	 */
	private function _render_array($data, $last_update)
	{
		$array = array();

		$array['last_update'] = $last_update;

		if(is_string($this->_currencies) && $this->_currencies === 'all')
		{
			foreach($data as $key => $value)
			{			
				preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

				$curr = substr($value->title, 0, 3);

				$array[$curr] = array(					
					'veteli'  => $matches[0][0],
					'eladasi' => $matches[0][1],
					'ikon'    => base_url().$this->_icon_path.$this->_icon_name.'-'.strtolower($curr).'.'.$this->_icon_ext
				);
			}
		}
		elseif(is_array($this->_currencies) && count($this->_currencies) > 0)
		{
			foreach($data as $key => $value)
			{			
				foreach($this->_currencies as $c)
				{				
					if(substr($value->title, 0, 3) == $c)
					{						
						preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

						$array[$c] = array(							
							'veteli'  => $matches[0][0],
							'eladasi' => $matches[0][1],
							'ikon'    => base_url().$this->_icon_path.$this->_icon_name.'-'.strtolower($c).'.'.$this->_icon_ext
						);

						// optimize performance
						$this->_currencies = array_diff($this->_currencies, array($c));
					}
				}

				// if currencies run out, don't continue foreach loop
				if( ! count($this->_currencies)) break;	
			}
		}
		else
		{
			throw new Exception("Error in config settings, currencies aren't set correctly.");			
		}

		return $array;
	}

}