<?php

namespace Kingsquare\Parser\Banking\Mt940\Engine;

use Kingsquare\Banking\Description;
use Kingsquare\Banking\Statement;
use Kingsquare\Banking\Transaction;


class Coba extends Spk
{
    /**
     * actual parsing of the data.
     *
     * @return Statement[]
     */
    public function parse()
    {
        $results = [];
        foreach ($this->parseStatementData() as $this->currentStatementData) {
            $statement = new Statement();
            if ($this->debug) {
                $statement->rawData = $this->currentStatementData;
            }
            $account = $this->parseStatementAccount();
            $statement->setBank($account[0]);
            $statement->setAccount($account[1]);
            $statement->setStartPrice($this->parseStatementStartPrice());
            $statement->setEndPrice($this->parseStatementEndPrice());
            $statement->setStartTimestamp($this->parseStatementStartTimestamp());
            $statement->setEndTimestamp($this->parseStatementEndTimestamp());
            $statement->setNumber($this->parseStatementNumber());

            foreach ($this->parseTransactionData() as $this->currentTransactionData) {
                $transaction = new Transaction();
                if ($this->debug) {
                    $transaction->rawData = $this->currentTransactionData;
                }
                $transaction->setAccount($this->parseTransactionAccount());
                $transaction->setAccountName($this->parseTransactionAccountName());
                $transaction->setPrice($this->parseTransactionPrice());
                $transaction->setDebitCredit($this->parseTransactionDebitCredit());
                $transaction->setCancellation($this->parseTransactionCancellation());
                $transaction->setDescription($this->parseTransactionDescription());
                $transaction->setValueTimestamp($this->parseTransactionValueTimestamp());
                $transaction->setEntryTimestamp($this->parseTransactionEntryTimestamp());
                $transaction->setTransactionCode($this->parseTransactionCode());
                $statement->addTransaction($transaction);
            }
            $results[] = $statement;
        }

        return $results;
    }

    /**
     * uses field 25 to gather accoutnumber.
     *
     * @return string accountnumber
     */
    protected function parseStatementAccount()
    {
        $results = [];
        if (preg_match('/:25:([\d\.]+)\/([\d\.]{9,10})([\d\w]{0,3})\r?\n/', $this->getCurrentStatementData(), $results)
            && !empty($results[1])
        ) {
            return array_slice($results, 1);
        }

        // SEPA / IBAN
        if (preg_match('/:25:([A-Z0-9]{8}[\d\.]+)*/', $this->getCurrentStatementData(), $results)
            && !empty($results[1])
        ) {
            return $this->sanitizeAccount($results[1]);
        }

        return '';
    }

    /**
     * uses the 86 field to determine retrieve the full description of the transaction.
     *
     * @return string
     */
    protected function parseTransactionDescription()
    {
        $results = [];
        if (preg_match('/:86:([\d]{3}(?=\?)|)(.*?)(?=(:6[12][\w]?:|$))/s', $this->getCurrentTransactionData(), $results)
            && !empty($results[2])
        ) {
            if ($results[1] !== '') {
                return $this->parseTransactionStructuredDescription($results[1], $results[2]);
            }
            return $this->sanitizeDescription($results[2]);
        }

        return '';
    }

    /**
     * @param string $string
     * @param string $inFormat
     *
     * @return int
     */
    protected function sanitizeTimestamp($string, $inFormat = 'ymd')
    {
        $date = \DateTime::createFromFormat($inFormat, $string, new \DateTimeZone('UTC'));
        $date->setTime(0, 0, 0);
        if ($date !== false) {
            return (int)$date->format('U');
        }

        return 0;
    }

    /**
     * uses the 86 field to determine retrieve the full description of the transaction.
     *
     * @return string
     */
    protected function parseTransactionStructuredDescription($transactionCode, $descriptionString)
    {
        $descriptionString = str_replace("\r", '', $descriptionString);
        $descriptionString = str_replace("\n", '', $descriptionString);
        $results = [];
        if (preg_match_all('/\?([01236][\d])(.*?)(?=\?[01236][\d]|$)/s', $descriptionString, $results)
            && !empty($results[1])
        ) {
            $identifier = '';
            $line = '';
            $description = new Description();
            $description->setPostingCode($transactionCode);
            foreach ($results[1] as $key => $field) {
                $intField = (int)$field;
                switch ($intField) {
                    case 0:
                        $description->setPostingText($results[2][$key]);
                        break;
                    case 10:
                        $description->setDaybookNumber($results[2][$key]);
                        break;
                    case ($intField >= 20 && $intField <= 29) || ($intField >= 60 && $intField <= 63):
                        if($this->getFieldIdentifier($results[2][$key]) === '') {
                            if($intField === 20) {
                                $identifier = 'SVWZ+';
                            }
                            $line .= $results[2][$key];
                        } else {
                            $identifier = $this->getFieldIdentifier($results[2][$key]);
                            $line = substr($results[2][$key], strlen($identifier));
                        }
                        $description->setField($identifier, $line);
                        break;
                    case 30:
                        $description->setBIC($results[2][$key]);
                        break;
                    case 31:
                        $description->setIBAN($results[2][$key]);
                        break;
                    case 32:
                        $description->setName($results[2][$key]);
                        break;
                    case 33:
                        $description->setName($description->getName() . $results[2][$key]);
                        break;
                }
            }
            return $description;
        }
        return null;
    }

    protected function getFieldIdentifier($string) {
        $results = [];
        if (preg_match('/^(EREF\+|KREF\+|MREF\+|CRED\+|DEBT\+|COAM\+|OAMT\+|SVWZ\+|ABWA\+|ABWE\+)/s', $string, $results)
            && !empty($results[1])
        ) {
            return $results[1];
        }
        return '';
    }
}