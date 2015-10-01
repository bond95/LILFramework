<?php
PathDriver::Using(array(PathDriver::UTILITIES => array("stringparser_bbcode.class")));
/**
 * Created by PhpStorm.
 * User: bohdan
 * Date: 08.09.15
 * Time: 19:34
 */
class BBDriver
{
    static private $bbcode;
    public static function ConfigureParser()
    {
        self::$bbcode = new StringParser_BBCode();

        self::$bbcode->addFilter (STRINGPARSER_FILTER_PRE, 'convertlinebreaks');


        self::$bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
        self::$bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'nl2br');
        self::$bbcode->addParser ('list', 'bbcode_stripcontents');



        self::$bbcode->addCode ('h1', 'simple_replace', null, array ('start_tag' => '<h1>', 'end_tag' => '</h1>'),
            'block', array ('listitem', 'block', 'link'), array ());

        self::$bbcode->addCode ('h2', 'simple_replace', null, array ('start_tag' => '<h2>', 'end_tag' => '</h2>'),
            'block', array ('listitem', 'block', 'link'), array ());

        self::$bbcode->addCode ('h3', 'simple_replace', null, array ('start_tag' => '<h3>', 'end_tag' => '</h3>'),
            'block', array ('listitem', 'block', 'link'), array ());

        self::$bbcode->addCode ('h4', 'simple_replace', null, array ('start_tag' => '<h4>', 'end_tag' => '</h4>'),
            'block', array ('listitem', 'block', 'link'), array ());

        self::$bbcode->addCode ('h5', 'simple_replace', null, array ('start_tag' => '<h5>', 'end_tag' => '</h5>'),
            'block', array ('listitem', 'block', 'link'), array ());

        self::$bbcode->addCode ('h6', 'simple_replace', null, array ('start_tag' => '<h6>', 'end_tag' => '</h6>'),
            'block', array ('listitem', 'block', 'link'), array ());


        self::$bbcode->setCodeFlag('h1', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
        self::$bbcode->setCodeFlag('h2', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
        self::$bbcode->setCodeFlag('h3', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
        self::$bbcode->setCodeFlag('h4', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
        self::$bbcode->setCodeFlag('h5', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
        self::$bbcode->setCodeFlag('h6', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);


        self::$bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'),
            'inline', array ('listitem', 'block', 'inline', 'link'), array ());

        self::$bbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'),
            'inline', array ('listitem', 'block', 'inline', 'link'), array ());

        self::$bbcode->addCode ('url', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'),
            'link', array ('listitem', 'block', 'inline'), array ('link'));

        self::$bbcode->addCode ('link', 'callback_replace_single', 'do_bbcode_url', array (),
            'link', array ('listitem', 'block', 'inline'), array ('link'));



        self::$bbcode->addCode ('list', 'callback_replace', 'do_bbcode_list', array (),
            'list', array ('block', 'listitem'), array ());

        self::$bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
            'listitem', array ('list'), array ());


        self::$bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);

        self::$bbcode->setCodeFlag ('*', 'paragraphs', true);

        self::$bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);

        self::$bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);

        self::$bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);

        self::$bbcode->setRootParagraphHandling (true);

        self::$bbcode->setParagraphHandlingParameters ("\n", '<p>', '</p>');

    }
    public static function ParseBB($code)
    {
//        $code = preg_replace('/\[(\/?)(b|i|u|s)\s*\]/', "<$1$2>", $code);
//        $code = preg_replace('/\[url\](?:http:\/\/)?([a-z0-9-.]+\.\w{2,4})\[\/url\]/', "<a href=\"http://$1\">$1</a>", $code);
//        $code = preg_replace('/\[url\s?=\s?([\'"]?)(?:http:\/\/)?([a-z0-9-.]+\.\w{2,4})\1\](.*?)\[\/url\]/', "<a href=\"http://$2\">$3</a>", $code);
//        $code = preg_replace('/\[(\/?)quote\]/', "<$1blockquote>", $code);
//        $code = preg_replace('/\[(\/?)list\]/', "<$1ul>", $code);
//        $code = preg_replace('/\[(\*|[0-9]+)\]([^\[]+)/', "<$1ul>", $code);
        return self::$bbcode->parse($code);

    }

}

function do_bbcode_url ($action, $attributes, $content, $params, $node_object) {
    if (!isset ($attributes['default'])) {
        $url = $content;
        $text = htmlspecialchars ($content);
    } else {
        $url = $attributes['default'];
        $text = $content;
    }

    if ($action == 'validate') {
        if (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:'
            || substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') {
            return false;
        }
        return true;
    }

    return '<a href="'.htmlspecialchars ($url).'">'.$text.'';
}

function do_bbcode_list($action, $attributes, $content, $params, $node_object)
{
    if (!isset ($attributes['default'])) {
        return '<ul>'.$content.'</ul>';
    } else {
        $start_id = $attributes['default'];
        return '<ol>'.$content.'</ol>';
    }
    return $content;
}

function bbcode_stripcontents ($text) {
    return preg_replace ("/[^\n]/", '', $text);
}

function convertlinebreaks ($text) {
    return preg_replace ("/\015\012|\015|\012/", "\n", $text);
}

BBDriver::ConfigureParser();