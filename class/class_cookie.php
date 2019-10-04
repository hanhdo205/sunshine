<?php

class SC_Cookie
{
    public $expire;
    public function __construct($day = COOKIE_EXPIRE)
    {    
        $this->expire = time() + ($day * 24 * 3600);
    }

    

    /**
     * @param string $key
     */
    public function setCookie($key, $val)
    {       
		
		setcookie($key, $val, $this->expire, ROOT_URLPATH, DOMAIN_NAME);
    }

    /**     
     *     
     * @param string $key
     */
    public function getCookie($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }
	
	 /**     
     *     
     * @param string $key
     */
    public function delCookie($key)
    {
       if(isset($_COOKIE[$key]))
	   {
			 unset($_COOKIE[$key]);   
			 return true;
	   }else
	   {
		    return false;
	   }
    }
}
