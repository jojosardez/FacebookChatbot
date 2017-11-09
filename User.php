<?php

class User {
    public function __construct($userDetails) {
        $this->firstName = $userDetails['first_name'];
        $this->lastName = $userDetails['last_name'];
        $this->profilePic = $userDetails['profile_pic'];
        $this->locale = $userDetails['locale'];
        $this->timezone = $userDetails['timezone'];
        $this->gender = $userDetails['gender'];
    }

    protected $firstName;
    protected $lastName;
    protected $profilePic;
    protected $locale;
    protected $timezone;
    protected $gender;

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getProfilePic() {
        return $this->profilePic;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function getGender() {
        return $this->gender;
    }
}