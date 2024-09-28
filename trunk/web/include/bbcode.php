<?php
/******************************************************************************
php-bbcode
  BBCode to HTML conversion, in PHP7.
Greg Kennedy <kennedy.greg@gmail.com>, 2018
  https://github.com/greg-kennedy/php-bbcode
This is public domain software.  Please see LICENSE for more details.
******************************************************************************/

class BBCode
{
  // Tag aliases.  Item on left translates to item on right.
  const TAG_ALIAS = [
    'url' => 'a',
    'code' => 'pre',
    'quote' => 'blockquote',
    '*' => 'li',
    'list' => 'ul'
  ];

  // helper function: normalize a potential "tag"
  //  convert to lowercase and check against the alias list
  //  returns a named array with details about the tag
  static private function decode_tag($input) : array
  {
    // first determine if it's opening on closing tag, then substr out the inner portion
    if ($input[1] === '/') {
      $open = 0;
      $inner = substr($input, 2, -1);
    } else {
      $open = 1;
      $inner = substr($input, 1, -1);
    }

    // oneliner to burst inner by spaces, then burst each of those by equals signs
    $params = array_map(
      function($a) { return explode('=', $a, 2); },
      explode(' ', $inner));

    // first "param" is special - it's the tag name and (optionally) the default arg
    $first = array_shift($params);

    // tag name
    $name = strtolower($first[0]);
    if (isset(self::TAG_ALIAS[$name])) {
      $name = self::TAG_ALIAS[$name];
    }

    // "default" (unnamed) argument
    $args = null;
    if (isset ($first[1])) {
      $args['default'] = $first[1];
      //echo  $first[1];
    }

    // finally, put the rest of the args in the list
    //array_walk( $params, function(&$a, $i, &$args) { print_r($args); $args[strtolower($a[1])] = $a[0]; }, $args);
    foreach ($params as &$param) {
      $k = isset($param[0]) ? strtolower($param[0]) : '';
      $v = isset($param[1]) ? $param[1] : '';
      $args[$k] = $v;
    }

    return [ 'name' => $name, 'open' => $open, 'args' => $args ];
  }

  // helper function: normalize HTML entities, with newline handling
  static private function encode($input) : string
  {
    return $input;
    // break substring into individual unicode chars
    $characters = preg_split('//u', $input, null, PREG_SPLIT_NO_EMPTY);

    // append each one-at-a-time to create output
    $lf = 0;
    $output = '';
    foreach ($characters as &$ch)
    {
      if ($ch === "\n") {
        $lf ++;
      } elseif ($ch === "\r") {
        continue;
      } else {
        if ($lf === 1) {
          $output .= "\n<br>";
          $lf = 0;
        } elseif ($lf > 1) {
          $output .= "\n\n<p>";
          $lf = 0;
        }

        if ($ch === '<') {
          $output .= '&lt;';
        } elseif ($ch === '>') {
          $output .= '&gt;';
        } elseif ($ch === '&') {
          $output .= '&amp;';
        } elseif ($ch === "\u{00A0}") {
          $output .= '&nbsp;';
        } else {
          $output .= $ch;
        }
      }
    }

    // trailing linefeed handle
    if ($lf === 1) {
      $output .= "\n<br>";
    } elseif ($lf > 1) {
      $output .= "\n\n<p>";
    }

    return $output;
  }

  // Renders a BBCode string to HTML, for inclusion into a document.
  static public function bbcode_to_html($input) : string
  {
    global $MSG_TOTAL;
    global $MSG_NUMBER_OF_PROBLEMS;

    // split input string into array using regex, UTF-8 aware
    //  this should give us tokens to work with

    // The regex is: one or more characters within square brackets,
    //  where the characters are any in this list (allowable URI chars):
    // ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 -._~:/?#@!$&'()*+,;=%
    // Square brackets are technically allowed, but excluded here, because they interfere.
    $match_count = preg_match_all("/\[[A-Za-z0-9 \-._~:\/?#@!$&'()*+,;=%]+\]/u",
      $input, $matches, PREG_OFFSET_CAPTURE);
    if ($match_count === FALSE) {
      throw new RuntimeException('Fatal error in preg_match_all for BBCode tags');
    }

    // begin with the empty string
    $output = '';
    $input_ptr = 0;
    $plist_color = Array('panel-success','panel-info','panel-warning','panel-danger');
    global $colorIndex;
   
    $stack = [];
    for ($match_idx = 0; $match_idx < $match_count; $match_idx ++)
    {
      list($match, $offset) = $matches[0][$match_idx];

      // pick up chars between tags and HTML-encode them
      $output .= self::encode(substr($input, $input_ptr, $offset - $input_ptr));
      // advance input_ptr to just past the current tag
      $input_ptr = $offset + strlen($match);

      // decode the tag 7.0 do not supported (16.04)
      //list('name' => $name, 'open' => $open, 'args' => $args) = self::decode_tag($match);
      $decode_data= self::decode_tag($match);
      $name=$decode_data['name'];
      $open=$decode_data['open'];
      $args=$decode_data['args'];
      if (! $open) {
        // CLOSING TAG

        // Search the tag stack and see if the opening tag was pushed into it
        if (array_search($name, $stack, TRUE) === FALSE) {
          // Attempted to close a tag that was not on the stack!
          $output = $output . self::encode($match);
        } else {
          //pop repeatedly until we pop the tag, and close everything on the way
          do {
            $popped_name = array_pop($stack);
            $output = $output . '</' . $popped_name . '>';
          } while ($name !== $popped_name);
        }
      } else {
        // OPENING TAG

        // Big if / elseif ladder to handle each tag
        if ($name === 'b' || $name === 'u' || $name === 'sup' || $name === 'sub' ||
            $name === 'blockquote' ||
            $name === 'ol' || $name === 'ul' ||
            $name === 'table') {
          // Simple tags (no validation or alternate modes)
          $stack[] = $name;
          $output = $output . '<' . $name . '>';
        } elseif ($name === 'li') {
          // Disallow [li] outside of [ol] or [ul]
          if (array_search('ol', $stack, TRUE) !== FALSE ||
              array_search('ul', $stack, TRUE) !== FALSE) {
            $stack[] = 'li';
            $output .= '<li>';
          } else {
            $output .= self::encode($match);
          }
        } elseif ($name === 'tr') {
          // Disallow [tr] outside of [table]
          if (array_search('table', $stack, TRUE) !== FALSE) {
            $stack[] = 'tr';
            $output .= '<tr>';
          } else {
            $output .= self::encode($match);
          }
        } elseif ($name === 'td' || $name === 'th') {
          // Disallow [th] / [td] outside of [tr] outside of [table]
          $tr_index = array_search('tr', $stack, TRUE);
          $table_index = array_search('table', $stack, TRUE);
          if ($tr_index !== FALSE && $table_index !== FALSE && $table_index < $tr_index) {
            $stack[] = $name;
            $output = $output . '<' . $name . '>';
          } else {
            $output .= self::encode($match);
          }

        } elseif ($name === 'font') {
          // Font size adjustment.  This requires an argument, one of "size" or "color" (or both).
          $font_param = [];

          if (isset ($args['size'])) {
//TODO: size validation
            $font_param['font-size'] = $args['size'];
          }
          if (isset ($args['color'])) {
//TODO: color validation
            $font_param['color'] = $args['color'];
          }
//TODO: handle bad settings

          if (! empty($font_param)) {
            $stack[] = 'font';

            // append all css_style params
            $css_style = [];
            foreach ($font_param as $name=>$value) {
              $css_style[] = $name . ': ' . $value;
            }
            $output = $output . '<span style="' . implode(';', $css_style) . '">';
          } else {
            // Font tag without good args is useless.
            $output .= self::encode($match);
          }

        // SPECIAL TAG HANDLING
        } elseif ($name === 'pre') {
          // [pre] / [code] put us into RAW mode, where nothing is parsed except [/code]

          for ($i = $match_idx + 1; $i < $match_count; $i ++)
          {
            list($search_match, $search_offset) = $matches[0][$i];
            $search_tag = self::decode_tag($search_match);
            if (! $search_tag['open'] && $search_tag['name'] === 'pre') { break; }
          }

          if ($i < $match_count) {
            // successfully found ending tag

            // encode everything contained between here and there
            $output = $output . '<pre>' . self::encode(substr($input, $input_ptr, $search_offset - $input_ptr)) . '</pre>';
            // advance ptr (again)
            $input_ptr = $search_offset + strlen($search_match);
            // update search position
            $match_idx = $i;
          } else {
            // Unrecognized type!
            $output .= self::encode($match);
          }
	} elseif ($name === 'md') {
          // markdown handling.  : [md]title[/md] .
          
          $buffer = null;
          $i = $match_idx + 1;
          if ($i < $match_count) {
            list($search_match, $search_offset) = $matches[0][$i];
            $search_tag = self::decode_tag($search_match);
            if (! $search_tag['open'] && $search_tag['name'] === 'md') {
              $buffer = substr($input, $input_ptr, $search_offset - $input_ptr);
            }
          }

          // matched something in the middle
          if (isset($buffer)) {
            //var_dump($colorIndex);
            $output = $output . '<div class="md" >'.$buffer.'</div>';
            // emit the tag
            // advance ptr (again)
            $input_ptr = $search_offset + strlen($search_match);
            // update search position
            $match_idx = $i;
          } else {
            // Unrecognized type!
            $output .= self::encode($match);
          }
        } elseif ($name === 'plist') {
          // Problem list handling.  modes: [plist=1000,1001,1002]title[/plist].
          //  Verify enclosing value first.
          $buffer = null;
          $i = $match_idx + 1;
          if ($i < $match_count) {
            list($search_match, $search_offset) = $matches[0][$i];
            $search_tag = self::decode_tag($search_match);
            if (! $search_tag['open'] && $search_tag['name'] === 'plist') {
              $buffer = substr($input, $input_ptr, $search_offset - $input_ptr);
            }
          }
          // matched something in the middle
          if (isset($buffer)) {
            if (isset($args['default'])) {
              // $buffer is the title
              $url = $args['default'];
            } else {
              // $buffer is the url
              $url = $buffer;
	    }
	    if(!isset($colorIndex)) $colorIndex =0;
	    $plist=html_entity_decode($url);
	    $pnum= count(explode(",",html_entity_decode($url)));
	    //var_dump($colorIndex);
            // emit the tag 如果希望题单显示2列，修改下面的col-lg-12为col-lg-6
	    $output = $output . '<div class="col-xs-12 col-lg-12"><div class="panel '.$plist_color[$colorIndex%count($plist_color)].'">'
				.'<div class="panel-heading" onclick="$(\'#plist'.$colorIndex.'\').load(\'problemset.php?ajax=1&list='.$url.'\').toggle()"  style="cursor: pointer" >'
		                .'<h4 class="panel-title" ><a class="collapsed" href="problemset.php?list=' . $url . '"  target="_blank">' 
		    		. self::encode($buffer) . '</a> <span class="pull-right">'.$MSG_TOTAL.' '.$pnum.' '.$MSG_NUMBER_OF_PROBLEMS.'</span> </h4> '
				.' </div><div id="plist'.$colorIndex.'"  style="display:none"  > </div></div></div>';
	    $colorIndex++;
            // advance ptr (again)
            $input_ptr = $search_offset + strlen($search_match);
            // update search position
            $match_idx = $i;
          } else {
            // Unrecognized type!
            $output .= self::encode($match);
          }
        } elseif ($name === 'a') {
          // URL handling.  Two modes: [a=url]title[/a] and [a]url[/a].
          //  Verify enclosing value first.
          $buffer = null;
          $i = $match_idx + 1;
          if ($i < $match_count) {
            list($search_match, $search_offset) = $matches[0][$i];
            $search_tag = self::decode_tag($search_match);
            if (! $search_tag['open'] && $search_tag['name'] === 'a') {
              $buffer = substr($input, $input_ptr, $search_offset - $input_ptr);
            }
          }

          // matched something in the middle
          if (isset($buffer)) {
            if (isset($args['default'])) {
              // $buffer is the title
              $url = $args['default'];
            } else {
              // $buffer is the url
              $url = $buffer;
            }
            // emit the tag
            $output = $output . '<a href="' . $url . '">' . self::encode($buffer) . '</a>';
            // advance ptr (again)
            $input_ptr = $search_offset + strlen($search_match);
            // update search position
            $match_idx = $i;
          } else {
            // Unrecognized type!
            $output .= self::encode($match);
          }

        } elseif ($name === 'img') {
          // image handling.  [img (optional=args go=here)]url[/img].
          //  Verify enclosing value first.
          $buffer = null;
          $i = $match_idx + 1;
          if ($i < $match_count) {
            list($search_match, $search_offset)  = $matches[0][$i];
            $search_tag = self::decode_tag($search_match);
            if (! $search_tag['open'] && $search_tag['name'] === 'img') {
              $buffer = substr($input, $input_ptr, $search_offset - $input_ptr);
            }
          }

          // matched something in the middle
          if (isset($buffer)) {
            // Image size adjustment - accepts width and height
            $img_param = [];

            if (isset ($args['width'])) {
  //TODO: size validation
              $img_param['width'] = $args['width'];
            }
            if (isset ($args['height'])) {
  //TODO: size validation
              $img_param['height'] = $args['height'];
            }
//TODO: handle bad settings

            // emit the tag
            $output = $output . '<img src="' . $buffer . '"';
            foreach ($img_param as $name=>$value) {
              $output = $output . ' ' . $name . '="' . $value . '"';
            }
            $output .= '>';

            // advance ptr (again)
            $input_ptr = $search_offset + strlen($search_match);
            // update search position
            $match_idx = $i;
          } else {
            // Unrecognized type!
            $output .= self::encode($match);
          }

        // ADD CUSTOM TAGS HERE

        } else {
          // Unrecognized type!
          $output .= self::encode($match);
        }
      }
    }
    // pick up any stray chars and HTML-encode them
    $output .= self::encode(substr($input, $input_ptr));
    // Close any remaining stray tags left on the stack
    while ($stack)
    {
      $tag = array_pop($stack);
      $output = $output . '</' . $tag . '>';
    }
    return $output;
  }
}
function filterDIV($input){
  $count1=substr_count($input,"<div");
  $count2=substr_count($input,"</div>");
  //  echo $count1."-".$count2."=".($count1-$count2)."<br>";
  if($count1!=$count2){
          $value=mb_ereg_replace("<[dD][iI][vV][a-zA-Z -_=\"\']*>","",$input);
          $value=mb_ereg_replace("</[dD][iI][vV]>","",$value);
          $value=mb_ereg_replace("<([^>]+)<","&lt;\\1&lt;",$value); //fixing 0<m<7000
          $value=mb_ereg_replace(">([^<]+)>","&gt;\\1&gt;",$value); //fixing  7000>m>7000
  }else{
         $value=$input;
  }
  return $value;
}

// procedural
function bbcode_to_html($input) : string
{
  global $OJ_DIV_FILTER;
  if(isset($OJ_DIV_FILTER)&&$OJ_DIV_FILTER) $input=filterDIV($input);
  return BBCode::bbcode_to_html($input);
}

