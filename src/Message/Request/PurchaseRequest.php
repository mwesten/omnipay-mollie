<?php

namespace Omnipay\Mollie\Message\Request;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Mollie\Message\Response\PurchaseResponse;

/**
 * Payment creation is elemental to the Mollie API: this is where most payment implementations start off. Note optional
 * parameters are accepted for certain payment methods. To wrap your head around the payment process, an explanation and
 * flow charts can be found in the Overview.
 *
 * @see https://docs.mollie.com/reference/v2/payments-api/create-payment
 *
 * @method PurchaseResponse send()
 */
class PurchaseRequest extends AbstractMollieRequest
{
    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->getParameter('metadata');
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setMetadata(array $value)
    {
        return $this->setParameter('metadata', $value);
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->getParameter('locale');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setLocale($value)
    {
        return $this->setParameter('locale', $value);
    }

    /**
     * @return string
     */
    public function getBillingEmail()
    {
        return $this->getParameter('billingEmail');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setBillingEmail($value)
    {
        return $this->setParameter('billingEmail', $value);
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('apiKey', 'amount', 'description', 'returnUrl');

        $data = [];
        $data['amount'] = [
            "value" => $this->getAmount(),
            "currency" => $this->getCurrency()
        ];
        $data['description'] = $this->getDescription();
        $data['redirectUrl'] = $this->getReturnUrl();
        $data['method'] = $this->getPaymentMethod();
        $data['metadata'] = $this->getMetadata();

        if ($this->getTransactionId()) {
            $data['metadata']['transactionId'] = $this->getTransactionId();
        }

        if ($issuer = $this->getIssuer()) {
            $data['issuer'] = $issuer;
        }

        $webhookUrl = $this->getNotifyUrl();
        if (null !== $webhookUrl) {
            $data['webhookUrl'] = $webhookUrl;
        }

        if ($locale = $this->getLocale()) {
            $data['locale'] = $locale;
        }

        if ($billingEmail = $this->getBillingEmail()) {
            $data['billingEmail'] = $billingEmail;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $response = $this->sendRequest(self::POST, '/payments', $data);

        return $this->response = new PurchaseResponse($this, $response);
    }
}
