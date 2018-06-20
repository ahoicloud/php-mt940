<?php

namespace Kingsquare\Banking;

use JsonSerializable;

class Description implements JsonSerializable
{

    private $postingCode = null;
    private $daybookNumber = null;
    private $postingText = '';
    private $usageText = '';
    private $iban = null;
    private $bic = null;
    private $creditorId = null;
    private $originatorsId = null;
    private $compensationAmount = null;
    private $originalAmount = null;
    private $altOriginator = null;
    private $altReceiver = null;
    private $mandateReference = null;
    private $endToEndReference = null;
    private $customerReference = null;
    private $name = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getEndToEndReference()
    {
        return $this->endToEndReference;
    }

    /**
     * @param null $endToEndReference
     */
    public function setEndToEndReference($endToEndReference)
    {
        $this->endToEndReference = $endToEndReference;
    }

    /**
     * @return null
     */
    public function getCustomerReference()
    {
        return $this->customerReference;
    }

    /**
     * @param null $customerReference
     */
    public function setCustomerReference($customerReference)
    {
        $this->customerReference = $customerReference;
    }

    /**
     * @return null
     */
    public function getMandateReference()
    {
        return $this->mandateReference;
    }

    /**
     * @param null $mandateReference
     */
    public function setMandateReference($mandateReference)
    {
        $this->mandateReference = $mandateReference;
    }

    /**
     * @return null
     */
    public function getCreditorId()
    {
        return $this->creditorId;
    }

    /**
     * @param null $creditorId
     */
    public function setCreditorId($creditorId)
    {
        $this->creditorId = $creditorId;
    }

    /**
     * @return null
     */
    public function getOriginatorsId()
    {
        return $this->originatorsId;
    }

    /**
     * @param null $originatorsId
     */
    public function setOriginatorsId($originatorsId)
    {
        $this->originatorsId = $originatorsId;
    }

    /**
     * @return null
     */
    public function getCompensationAmount()
    {
        return $this->compensationAmount;
    }

    /**
     * @param null $compensationAmount
     */
    public function setCompensationAmount($compensationAmount)
    {
        $this->compensationAmount = $compensationAmount;
    }

    /**
     * @return null
     */
    public function getOriginalAmount()
    {
        return $this->originalAmount;
    }

    /**
     * @param null $originalAmount
     */
    public function setOriginalAmount($originalAmount)
    {
        $this->originalAmount = $originalAmount;
    }

    /**
     * @return null
     */
    public function getAltOriginator()
    {
        return $this->altOriginator;
    }

    /**
     * @param null $altOriginator
     */
    public function setAltOriginator($altOriginator)
    {
        $this->altOriginator = $altOriginator;
    }

    /**
     * @return null
     */
    public function getAltReceiver()
    {
        return $this->altReceiver;
    }

    /**
     * @param null $altReceiver
     */
    public function setAltReceiver($altReceiver)
    {
        $this->altReceiver = $altReceiver;
    }

    /**
     * @return string
     */
    public function getPostingCode()
    {
        return $this->postingCode;
    }

    /**
     * @param string $postingCode
     */
    public function setPostingCode(string $postingCode)
    {
        $this->postingCode = $postingCode;
    }

    /**
     * @return string
     */
    public function getDaybookNumber()
    {
        return $this->daybookNumber;
    }

    /**
     * @param string $daybookNumber
     */
    public function setDaybookNumber(string $daybookNumber)
    {
        $this->daybookNumber = $daybookNumber;
    }

    /**
     * @return string
     */
    public function getPostingText()
    {
        return $this->postingText;
    }

    /**
     * @param string $postingText
     */
    public function setPostingText(string $postingText)
    {
        $this->postingText = $postingText;
    }

    /**
     * @return string
     */
    public function getUsageText()
    {
        return $this->usageText;
    }

    /**
     * @param string $usageText
     */
    public function setUsageText(string $usageText)
    {
        $this->usageText = $usageText;
    }

    /**
     * @return string
     */
    public function getIBAN()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIBAN(string $iban)
    {
        $this->iban = $iban;
    }

    /**
     * @return string
     */
    public function getBIC()
    {
        return $this->bic;
    }

    /**
     * @param string $bic
     */
    public function setBIC(string $bic)
    {
        $this->bic = $bic;
    }

    public function setField($identifier, $value)
    {
        switch ($identifier) {
            case 'EREF+':
                $this->setEndToEndReference($value);
                break;
            case 'KREF+':
                $this->setCustomerReference($value);
                break;
            case 'MREF+':
                $this->setMandateReference($value);
                break;
            case 'CRED+':
                $this->setCreditorId($value);
                break;
            case 'DEBT+':
                $this->setOriginatorsId($value);
                break;
            case 'COAM+':
                $this->setCompensationAmount($value);
                break;
            case 'OAMT+':
                $this->setOriginalAmount($value);
                break;
            case 'SVWZ+':
                $this->setUsageText($value);
                break;
            case 'ABWA+':
                $this->setAltOriginator($value);
                break;
            case 'ABWE+':
                $this->setAltReceiver($value);
                break;
        }
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return get_object_vars($this);
    }
}