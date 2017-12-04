<?php

namespace App;

use App\Database\DB;

class URLShortener
{

    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /*
     * Create short URL. $destination_url is the destination URL
     */
    public function createShortURL($destination_url)
    {
        $url_code = $this->generateURLCode(7);

        $add = $this->db->prepare("INSERT INTO urls(code, destination, created_at, updated_at) 
VALUES(:code, :destination, NOW(), NOW())");
        $add->execute(array(
            ':code' => $url_code,
            ':destination' => $destination_url,
        ));

        return $this->db->lastInsertId();
    }

    /*
     *  Get URL by URL ID
     */
    public function getShortURLByID($url_id)
    {
        $select = $this->db->prepare("SELECT * FROM urls WHERE id = :id");
        $select->execute(array(
           ':id' => $url_id,
        ));

        return $select->fetch();
    }

    /*
     *  Get URL by URL code
     */
    public function getShortURLByCode($url_code)
    {
        $select = $this->db->prepare("SELECT * FROM `urls` WHERE code = :code");
        $select->execute(array(
            ':code' => $url_code,
        ));

        return $select->fetch();
    }

    /*
     *  Retrieve all URLs
     */
    public function getAllURLs()
    {
        $select = $this->db->query("SELECT * FROM `urls`");
        return $select->fetchAll();
    }

    /*
     *  Delete URL via URL ID
     */
    public function deleteURL($url_id)
    {
        $delete = $this->db->prepare("DELETE FROM `urls` WHERE id = :id");
        $delete->execute(array(
           ':id' => $url_id,
        ));
    }

    /*
     *  Function for randomness
     */
    public function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min;
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);
        return $min + $rnd;
    }

    /*
     *  Generates unique URL code
     */
    public function generateURLCode($length)
    {
        $url = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $url .= $codeAlphabet[$this->cryptoRandSecure(0, $max-1)];
        }

        if($this->UrlExistsInDB($url)) { $this->generateURLCode(7); }

        return $url;
    }

    /*
     *  Checks whether URL code exists in database
     */
    public function URLExistsInDB($url_code)
    {
        $query = $this->db->prepare("SELECT id FROM `urls` WHERE code = :code");
        $query->execute(array(
            ':code' => $url_code,
        ));

        $row = $query->fetch();

        if($row) return true;
        else return false;
    }

    /*
     *  Create entry in `url_visits` table.
     */
    public function visitURL($url)
    {
        $url_visit = $this->db->prepare("INSERT INTO `url_visits`(url_id, code, created_at) VALUES(:url_id, :code, NOW())");
        $url_visit->execute(array(
            ':url_id' => $url['id'],
            ':code' => $url['code'],
        ));
    }

    /*
     * Get all URL visits
     */
    public function getURLVisits($url_id)
    {
        $select = $this->db->prepare("SELECT * FROM `url_visits` WHERE url_id = :id ORDER BY created_at DESC");
        $select->execute(array(
           ':id' => $url_id,
        ));

        return $select->fetchAll();
    }

    /*
     *  Get how many times URL has been visited. Accepted parameters are URL ID and URL Code
     */
    public function getTotalVisits($id = 0, $url_code = null)
    {
        if($id != 0)
        {
            $select = $this->db->prepare("SELECT COUNT(id) as visits FROM `url_visits` WHERE url_id = :id");
            $select->execute(array(
                ':id' => $id,
            ));

            $res = $select->fetch();
        }
        else if($url_code !== null)
        {
            $select = $this->db->prepare("SELECT COUNT(id) as visits FROM `url_visits` WHERE code = :code");
            $select->execute(array(
                ':code' => $url_code,
            ));

            $res = $select->fetch();
        }

        return $res['visits'];
    }

    /*
     *  Delete URL visits by passing either URL ID or URL Code
     */
    public function deleteURLVisits($id = 0, $url_code = null)
    {
        if($id != 0)
        {
            $delete = $this->db->prepare("DELETE FROM `url_visits` WHERE url_id = :id");
            $delete->execute(array(
               ':id' => $id,
            ));
        }
        else if($url_code !== null)
        {
            $delete = $this->db->prepare("DELETE FROM `url_visits` WHERE code = :code");
            $delete->execute(array(
                ':code' => $url_code,
            ));
        }
    }

}
