<?php
require_once "classes/User.php";
require_once "classes/Account.php";
require_once "classes/helper/Database.php";

class Users
{
    private string $name;
    private string $email;
    private string $type;
    private $timestamp;
    private int $ldap;
    private int $google;
    private int $registration;

    public function getAllUsers() {
        return "<tr>
                    <td>$this->name</td>
                    <td>$this->email</td>
                    <td>$this->type</td> 
                    <td>$this->timestamp</td> 
                </tr>";
    }

    /**
     * @return int
     */
    public function getLdap(): int
    {
        return $this->ldap;
    }

    /**
     * @return int
     */
    public function getGoogle(): int
    {
        return $this->google;
    }

    /**
     * @return int
     */
    public function getRegistration(): int
    {
        return $this->registration;
    }



}