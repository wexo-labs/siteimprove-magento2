<?php
declare(strict_types=1);

namespace Siteimprove\Magento\Setup;

use Magento\Framework\FlagManager,
    Siteimprove\Magento\Model\Token,
    Magento\Framework\HTTP\ClientFactory,
    Magento\Framework\Serialize\Serializer\Json;

class TokenSetup
{

    /**
     * @var Json
     */
    protected $_json;

    /**
     * @var Token
     */
    protected $_token;

    /**
     * @var FlagManager
     */
    protected $_flagManager;

    /**
     * @var ClientFactory
     */
    protected $_clientFactory;

    public function __construct(
        Json $json,
        Token $token,
        FlagManager $flagManager,
        ClientFactory $clientFactory
    ) {
        $this->_json = $json;
        $this->_token = $token;
        $this->_flagManager = $flagManager;
        $this->_clientFactory = $clientFactory;
    }

    /**
     * Ensure Magento have a Siteimprove Token
     */
    public function ensureTokenIsFetched()
    {
        if (!$this->_token->getToken()) {
            $client = $this->_clientFactory->create();
            $client->get('https://my2.siteimprove.com/auth/token?cms=Magento2');

            if ($client->getStatus() === 200) {
                $token = $this->_json->unserialize($client->getBody())['token'] ?? null;
                if ($token) {
                    $this->_flagManager->saveFlag('siteimprove_token', $token);
                }
            }
        }
    }
}
