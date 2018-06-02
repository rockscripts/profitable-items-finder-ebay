<?php



require_once 'EbatNs_Logger.php';

require_once 'EbatNs_Session.php';

require_once 'EbatNs_ServiceProxy.php';

require_once 'AbstractResponseType.php';

require_once 'AckCodeType.php';



/**

 * Prepares an environment for testing ebatNs calls

 *

 */

class EbatNs_Environment

{

    /**

     * @var EbatNs_Session

     */

    protected $session;



    /**

     * @var EbatNs_Logger

     */

    protected $logger = null;

    

    /**

     * @var EbatNs_ServiceProxy

     */

    protected $proxy = null;

    

    /**

     * Fullpath to config-file

     *

     * @var string

     */

    protected $configFile = null;

    

    public function __construct($logLevel = 0, $configFile = 'config/ebay.config.php')

    {
        foreach($_SESSION as $session):
            foreach($session as $key => $value):
            if($key=="user_id"):
               $configFile =   "config/{$value}/ebay.config.php";
            endif;
            endforeach;
        endforeach;
        $this->init($logLevel, $configFile);

    }

    

    public function init($logLevel = 0, $configFile)

    {

        if ($logLevel >= 1)

            $this->logger = new EbatNs_Logger();

        $this->configFile = $configFile;

        $this->session = new EbatNs_Session($this->configFile);

        $this->proxy = new EbatNs_ServiceProxy($this->session);

        

        if ($this->logger)

	        $this->proxy->attachLogger($this->logger);

    }

    

    public function dispatchCall()

    {

    }

    

    /**

     * Tests if a response is valid and prints out

     * a error trace if not

     *

     * @param AbstractResponseType $res

     * @return boolean

     */

    protected function testValid($res)

    {

        if ($res->getAck() == AckCodeType::CodeType_Success)

        {

            return true;

        }

        else

        {

            return $this->proxy->getErrorsToString($res, true);

        }

    }

    

    protected function dumpObject($data)

    {

        echo  "<br>---------------------<br>"

            . "Object Dump:<br><pre>" . print_r($data, true) . "</pre>";

    }

}

?>

