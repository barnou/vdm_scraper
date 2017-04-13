<?php

namespace App\DaO;

use Slim\Exception\MethodNotAllowedException;

use Lib\Lib\DB;

class PostDao {

    public static function getPosts($params) {

        $db = DB::getDB('DATA');
        $where = "";
        $para = [];
        
        if ($db) {

            if(count($params) > 0) {
                $dateRegexp = "/\d{4}-(?:0[1-9]|1[1-2])-(?:[012][0-9]|3[0-1])/";
                $where = "WHERE 1 = 1 ";
                if(isset($params['author'])) {
                    $where .= "AND post_author ILIKE ? ";
                    $para[] = '%'.$params['author'].'%';
                }
                if(isset($params['from'])) {
                    if(preg_match($dateRegexp, $params['from']) != 1) {
                        throw new \InvalidArgumentException("Invalid from date", 401);
                    }
                    $where .= "AND post_datelog >= ?::timestamp ";
                    $para[] = $params['from'];
                }
                if(isset($params['to'])) {
                    if(preg_match($dateRegexp, $params['to']) != 1) {
                        throw new \InvalidArgumentException("Invalid to date", 401);
                    }
                    $where .= "AND post_datelog <= ?::timestamp ";
                    $para[] = $params['to'];
                }
                if(isset($params['city'])) {
                    $where .= "AND post_city ILIKE ? ";
                    $para[] = '%'.$params['city'].'%';
                }
                if(isset($params['country'])) {
                    $where .= "AND post_country ILIKE ? ";
                    $para[] = '%'.$params['country'].'%';
                }
            }
            $sql = "
                SELECT *
                FROM posts
                ".$where."
                ORDER BY post_datelog DESC;
            ";

            $qry = $db->prepare($sql);
            $qry->execute($para);
            $results = $qry->fetchAll(\PDO::FETCH_ASSOC);
            $res = ["posts" => $results, "count" => count($results)];
            return $res;
        }

        return false;
    }

    public static function getPost($postId) {
        $db = DB::getDB('DATA');

        if ($db) {
            $sql = "
                SELECT *
                FROM posts
                WHERE post_uid = ?
                ORDER BY post_datelog DESC;
            ";

            $qry = $db->prepare($sql);
            $qry->execute(Array($postId));
            $result = $qry->fetch(\PDO::FETCH_ASSOC);
            if($result == false) { $result = [];}
            $res = ["post" => $result];
            return $res;
        }

        return false;
    }
}