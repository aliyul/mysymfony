<?php

namespace walisymfony\waliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Collection;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Nasabah
 *
 * @ORM\Table(name="nasabah")
 * @ORM\Entity
 */
class Nasabah
{
    /**
     * @var string
     *
     * @ORM\Column(name="nama", type="string", length=50, nullable=true)
     */
    private $nama;

    /**
     * @var integer
     *
     * @ORM\Column(name="nocredit", type="string", length=16, nullable=true)
     */
    private $nocredit;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set nama
     *
     * @param string $nama
     *
     * @return Nasabah
     */
    public function setNama($nama)
    {
        /**
        *$this->nama = validateRegisterData(array($nama));
         * */
        $this->nama = $nama;

        return $this;
    }

    /**
     * Get nama
     *
     * @return string
     */
    public function getNama()
    {
        return $this->nama;
    }

    /**
     * Set nocredit
     *
     * @param integer $nocredit
     *
     * @return Nasabah
     */
    public function setNocredit($nocredit)
    {
        $this->nocredit = $nocredit;

        return $this;


    }

    /**
     * Get nocredit
     *
     * @return string
     */
    public function getNocredit()
    {
        return $this->nocredit;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function validateRegisterData(array $registerData)

    {
        /**
         * Imagine that we get an array with these keys: email, pass, postcode and termandco (which is actually Ulabox register data needed)
         */
        $collectionConstraint = new Collection(array(
            $this->nama => array(
                new NotBlank(),
                new $this->nama(),
                new Length(array('min' => 5)),
            ),
            $this->nocredit  => array(
                new NotBlank(),
                new nocredit(),
                new Length(array('min' => 16)),
                #new Callback(array('methods' => array(
                #    array($this, 'CreditCardType')
                #))),
            ),
        ));

        /**
         * validateValue expects either a scalar value and its constraint or an array and a constraint Collection (which actually extends Constraint)
         */
        $errors = $this->container->get('validator')->validateValue($registerData, $collectionConstraint);

        /**
         * To use symfony2 default validation errors, we must call it this way...
         * Count is used as this is not an array but a ConstraintViolationList
         */
        if (count($errors) !== 0) {
            throw new HttpException(400, $errors[0]->getPropertyPath() . ':' . $this->container->get('translator')->trans($errors[0]->getMessage(), array(), 'validators'));
            return false;
        }
        return true;
    }
    public function CreditCardType($CardNo)
    {
        /*
        '*CARD TYPES            *PREFIX           *WIDTH
        'American Express       34, 37            15
        'Diners Club            300 to 305, 36    14
        'Carte Blanche          38                14
        'Discover               6011              16
        'EnRoute                2014, 2149        15
        'JCB                    3                 16
        'JCB                    2131, 1800        15
        'Master Card            51 to 55          16
        'Visa                   4                 13, 16
        */
//Just in case nothing is found
        $CreditCardType = "Unknown";

//Remove all spaces and dashes from the passed string
        $CardNo = str_replace("-", "",str_replace(" ", "",$CardNo));

//Check that the minimum length of the string isn't less
//than fourteen characters and -is- numeric
        If(strlen($CardNo) < 14 || !is_numeric($CardNo))
            return false;

//Check the first two digits first
        switch(substr($CardNo,0, 2))
        {
            Case 34: Case 37:
            $CreditCardType = "American Express";
            break;
            Case 36:
                $CreditCardType = "Diners Club";
                break;
            Case 38:
                $CreditCardType = "Carte Blanche";
                break;
            Case 51: Case 52: Case 53: Case 54: Case 55:
            $CreditCardType = "Master Card";
            break;
        }

//None of the above - so check the
        if($CreditCardType == "Unknown")
        {
            //first four digits collectively
            switch(substr($CardNo,0, 4))
            {
                Case 2014:Case 2149:
                $CreditCardType = "EnRoute";
                break;
                Case 2131:Case  1800:
                $CreditCardType = "JCB";
                break;
                Case 6011:
                    $CreditCardType = "Discover";
                    break;
            }
        }

//None of the above - so check the
        if($CreditCardType == "Unknown")
        {
            //first three digits collectively
            if(substr($CardNo,0, 3) >= 300 && substr($CardNo,0, 3) <= 305)
            {
                $CreditCardType = "American Diners Club";
            }
        }

//None of the above -
        if($CreditCardType == "Unknown")
        {
            //So simply check the first digit
            switch(substr($CardNo,0, 1))
            {
                Case 3:
                    $CreditCardType = "JCB";
                    break;
                Case 4:
                    $CreditCardType = "Visa";
                    break;
            }
        }

        return $CreditCardType;
    }
}

