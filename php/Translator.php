<?php
class Translator{ 
    public static function GoogleTranslate($text,$from="zh-CN",$to="en"){
        $query=http_build_query(array('langpair'=>"$from|$to",'text'=>$text));
        $url="http://translate.google.com/translate_t";
        $context=stream_context_create(array(
            "http"=>array(
                "method"=>"POST",
                "content"=>"langpair=$from|$to&text=$text"
            )
        ));
        $contents=file_get_contents($url,FILE_TEXT,$context);
        $regex='#<span id=result_box[^>]*><span[^>]*>([^>]*)</span>#';
        preg_match($regex,$contents,$match);
        $content=$match[1];
        return $content;
    }
}
if(preg_match("#/".basename(__file__)."$#",$_SERVER["PHP_SELF"])){
    echo Translator::GoogleTranslate("中国科学技术大学");
}
?>
