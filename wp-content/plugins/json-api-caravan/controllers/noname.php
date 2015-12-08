<?php

/*
  Controller name: noname
  Controller description: whatever
  Controller Author: IanXia
  
*/
class JSON_API_Noname_Controller {

public function info(){	  

	  	global $json_api;

   		return array(
				"version" => DAYE_VERSION				
		   );	   

	  }
public function buildSlugArgs($taxonomy, $value)
{
    if(!($taxonomy || $value))
        return NULL;
    $operator = 'IN';
    if($value[0] == '!')
        $operator = 'NOT IN';
    $value = trim($value, "! \t\n\r\0\x0B");
    $values = explode(',', $value);
    $ret = array(
            'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $values,
            'operator' => $operator
        );
    return $ret;
}
public function buildMetaArgs($taxonomy, $value)
{

    $values = explode(',', $value);
    $ret = array(
            'relation' => 'AND'
        );
    $isSet = false;
    foreach($values as  $tmp)
    {
        $tmp = trim($tmp, "() \t\n\r\0\x0B");
        $tmps = explode(':', $tmp);
        if(count($tmps) == 2 && count($tmps[0]) > 0 && count($tmps[1]) > 0)
        {
            $isSet = true;
            $arr = array(
                    'key'   => $tmps[0],
                    'value' => $tmps[1]
                );
            $ret[] = $arr;
        }
    }
    $ret = $isSet ? $ret : NULL;
    return $ret;
}

public function posts_result($posts) {
    global $wp_query;
    return array(
      'count' => count($posts),
      'count_total' => (int) $wp_query->found_posts,
      'pages' => $wp_query->max_num_pages,
      'posts' => $posts
    );
}

public function get_custom_posts() {
        global $json_api;
        $slug = NULL;
        $meta = NULL;
        $tags = NULL;
        if($json_api->query->category)
        {
            $slug = JSON_API_Noname_Controller::buildSlugArgs('category', $json_api->query->category);
        }
        if($json_api->query->metas)
        {
            $meta = JSON_API_Noname_Controller::buildMetaArgs('metas', $json_api->query->metas);
        }
        //if($json_api->query->tag)
        //{
         //   $tags = array('tag' => $json_api->query->tags);
       //}
        // Make sure we have key/value query vars
        if (!$slug && !$meta && $tags) {
            $json_api->error("Null args.");
        }
        $args = array();
        
        if($slug)
        {
            $tax_query =  array('relation' => 'AND');
            $tax_query[] = $slug;
            $args['tax_query'] = $tax_query;
        }
        if($json_api->query->tags)
        {            
            $args['tag'] = $json_api->query->tags;
        }
        if($meta)
            $args['meta_query'] = $meta;
        // See also: http://codex.wordpress.org/Template_Tags/query_posts
        $posts = $json_api->introspector->get_posts($args);
        //var_dump($posts);
        $result  = JSON_API_Noname_Controller::posts_result($posts);
        return $result;
    }	  
 
}
 
 