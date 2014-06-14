<?php

class LimepieStandard_Sniffs_Indent_LineSniff implements PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {

        $lines = array();
        $c = 0;
        $tokens = $phpcsFile->getTokens();
        $a = [];
        foreach($tokens as $key => $value) {
            /*
            if($value['type'] == 'T_IF') {
                if($tokens[$key-1]['type'] == 'T_WHITESPACE') {
                    if($tokens[$key-2]['type'] == 'T_ELSE') {
                        $error = 'else if말고 elseif를 사용하세요';
                        $data  = array();
                        $phpcsFile->addError($error, $key, 'elseif', $data);
                    }
                }
            }
            */
            $lines[$value['line']][$key] = $value;
        }
$lines[][] = ['type' => 0];
$lines[][] = ['type' => 0];

        $slice = explode("\n",$phpcsFile->fixer->getContents());

        $prev = array(
            'content'   => '',
            'column'    => 1,
            'start'     => '',
            'first'     => '',
            'last'      =>''            
        );
        $pos = 0;
        foreach($slice as $key => $value) {
            $line = $key + 1;

            if(isset($lines[$line]) == false) {
                continue;
            }
            $value_length = strlen($value);
            $pos += $value_length;
            $content = ltrim($value);
            $content_length = strlen($content);

            if($content_length == 0) {
                continue;
            }
            $column = $value_length - $content_length + 1;
            $tmp = explode(" ", $content);
            $start = $tmp[0];
            $first = substr($content,0,1);
            $last =  substr(rtrim($content),-1);//$tmp[count($tmp)-1]
            

            if(in_array($prev['last'], array(
                '{',
                '[',
                '(',
            ))) {
                $step = 4;
            } else if(
                in_array($first, array(
                    '}',
                    ']',
                    ')'
                )) 
                ||
                in_array($start, array(
                    '},', //closure 
                ))    
            ){
                $step = -4;
            } else {
                $step = 0;
            }
            if($prev['column'] + $step == $column) {
            
            } else {

                $error = ', %s found : %s';
                $data  = array($column, $prev['column'] + $step);

                end($lines[$key]);
                $key2 = key($lines[$key]);

                $phpcsFile->addError($error, $key2, 'indent', $data);
            
            }
            $prev = array(
                'content'   => $content,
                'column'    => $prev['column'] + $step,
                'start'     => $start,
                'first'     => $first,
                'last'      => $last
            ); 
/*
            foreach($lines[$line] as $pos => $v2) {
                if(in_array($v2['type'], array('T_IF', 'T_FOREACH', 'T_FOR', 'T_TRY'))) {

                    // if 전에 공란이 아니다.
                    if($tokens[$pos - 1]['type'] == 'T_WHITESPACE') {

                    } else {
                        $error = $v2['content'].' 이전에 공란이 필요합니다.';
                        $data  = array();
                        $phpcsFile->addError($error, $pos, 'new line', $data);

                    }

                    // else가 있다면 위에서 elseif 유도, 중복 에러 방지차원
                    if($tokens[$pos-2]['type'] == 'T_ELSE') {
                        
                    } else {// else if가 아니고
                        if(count($lines[$line - 1]) == 1) {// 하나만 있다면 무조건 공란
                        } else {
                            $error = '이전 행에 공란이 필요합니다.';
                            $data  = array();
                            $phpcsFile->addError($error, $pos, 'blank', $data);
                        }
                    }

                }
                if($v2['type'] == 'T_CLOSE_CURLY_BRACKET') {
                    echo "\n";
if($tokens[$v2['scope_opener']-5]['type'] == 'T_CLOSURE') {
pr($tokens[$v2['scope_opener']-5]);
pr($tokens[327]);
}
                    if($tokens[$pos + 1]['type'] == 'T_WHITESPACE') {
                    } else {
                        $error = $v2['content'].' 이후에 공란이 필요합니다.';
                        $data  = array();
                        $phpcsFile->addError($error, $pos, 'new line', $data);

                    }
                    if(in_array(@$tokens[$pos+2]['type'], ['T_ELSE', 'T_ELSEIF'])) {
                    } else {
                        if(count($lines[$line + 1]) == 1) {// 하나만 있다면 무조건 공란
                        } else {
                            $error = '이후 행에 공란이 필요합니다.';
                            $data  = array();
                            $phpcsFile->addError($error, $pos, 'blank', $data);
                        }
                    }
                }
            }
*/
        }
    }//end _shouldIgnoreUse()


}//end class


?>
