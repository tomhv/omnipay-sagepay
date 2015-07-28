<?php

namespace Omnipay\SagePay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Sage Pay Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://live.sagepay.com/gateway/service';
    protected $testEndpoint = 'https://test.sagepay.com/gateway/service';

    public function getVendor()
    {
        return $this->getParameter('vendor');
    }

    public function setVendor($value)
    {
        return $this->setParameter('vendor', $value);
    }

    public function getService()
    {
        return $this->action;
    }

    public function getAccountType()
    {
        return $this->getParameter('accountType');
    }

    /**
     * Set account type.
     * 
     * This is ignored for all PAYPAL transactions.
     * 
     * @param string $value E: Use the e-commerce merchant account. (default)
     *                      M: Use the mail/telephone order account. (if present)
     *                      C: Use the continuous authority merchant account. (if present)
     */
    public function setAccountType($value)
    {
        return $this->setParameter('accountType', $value);
    }

    public function getReferrerId()
    {
        return $this->getParameter('referrerId');
    }

    /**
     * Set the referrer ID for PAYMENT, DEFERRED and AUTHENTICATE transactions.
     */
    public function setReferrerId($value)
    {
        return $this->setParameter('referrerId', $value);
    }

    public function getApplyAVSCV2()
    {
        return $this->getParameter('applyAVSCV2');
    }

    /**
     * Set the apply AVSCV2 checks.
     * 
     * @param  int $value 0: If AVS/CV2 enabled then check them. If rules apply, use rules. (default)
     *                    1: Force AVS/CV2 checks even if not enabled for the account. If rules apply
     *                       use rules.
     *                    2: Force NO AVS/CV2 checks even if enabled on account.
     *                    3: Force AVS/CV2 checks even if not enabled for account but DON'T apply any
     *                       rules.
     */
    public function setApplyAVSCV2($value)
    {
        return $this->setParameter('applyAVSCV2', $value);
    }

    public function getApply3DSecure()
    {
        return $this->getParameter('apply3DSecure');
    }

    /**
     * Whether or not to apply 3D secure authentication.
     * 
     * This is ignored for PAYPAL, EUROPEAN PAYMENT transactions.
     * 
     * @param  int $value 0: If 3D-Secure checks are possible and rules allow, perform the
     *                       checks and apply the authorisation rules. (default)
     *                    1: Force 3D-Secure checks for this transaction if possible and
     *                       apply rules for authorisation.
     *                    2: Do not perform 3D-Secure checks for this transactios and always
     *                       authorise.
     *                    3: Force 3D-Secure checks for this transaction if possible but ALWAYS
     *                       obtain an auth code, irrespective of rule base.
     */
    public function setApply3DSecure($value)
    {
        return $this->setParameter('apply3DSecure', $value);
    }

    protected function getBaseData()
    {
        $data = array();
        $data['VPSProtocol'] = '3.00';
        $data['TxType'] = $this->action;
        $data['Vendor'] = $this->getVendor();
        $data['AccountType'] = $this->getAccountType() ?: 'E';

        return $data;
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $data)->send();

        return $this->createResponse($httpResponse->getBody());
    }

    public function getEndpoint()
    {
        $service = strtolower($this->getService());

        if ($this->getTestMode()) {
            return $this->testEndpoint."/$service.vsp";
        }

        return $this->liveEndpoint."/$service.vsp";
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    /**
     * Whether or not to request a token with this request.
     *
     * @param  int $value 0: Do not have the gateway return a token in the response
     *                    1: Ask the gateway to return a token in the response
     */
    public function setCreateToken($value)
    {
        $this->setParameter('createToken', $value);
    }

    public function getCreateToken()
    {
        return $this->getParameter('createToken', 0);
    }

    /**
     * Whether or not to store the existing token when you use it
     *
     * @param  int $value 0: Token used in this request will no be available in
     *                       the future
     *                    1: Token used in this request will remain available
     *                       for future use
     */
    public function setStoreToken($value)
    {
        $this->setParameter('storeToken', $value);
    }

    public function getStoreToken()
    {
        return $this->getParameter('storeToken', 0);
    }
}
