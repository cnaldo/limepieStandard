<?php
/**
 * LimepieStandard_Sniffs_WhiteSpace_FunctionOpeningBraceSpaceSniff.
*/

class LimepieStandard_Sniffs_WhiteSpace_FunctionOpeningBraceSpace1Sniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION, T_CLOSURE);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            // Probably an interface method.
            return;
        }

        $openBrace   = $tokens[$stackPtr]['scope_opener'];
        $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($openBrace + 1), null, true);
        if ($nextContent === $tokens[$stackPtr]['scope_closer']) {
             // The next bit of content is the closing brace, so this
             // is an empty function and should have a blank line
             // between the opening and closing braces.
            return;
        }


        $braceLine = $tokens[$openBrace]['line'];
        $nextLine  = $tokens[$nextContent]['line'];


        $found = ($nextLine - $braceLine - 1); // 1이면 하나가 있다.

        if ($found == 1) {
        } else if($found == 0){ // 0일때만 오류를 내야 linespace와 충돌하지 않는다.
            $error = 'Expected 1 blank lines after opening function brace; %s found';
            $data  = array($found);
            $phpcsFile->addError($error, $openBrace, 'SpacingAfter', $data);
        }

        if ($phpcsFile->tokenizerType === 'JS') {
            // Do some additional checking before the function brace.
            $nestedFunction = ($phpcsFile->hasCondition($stackPtr, T_FUNCTION) === true || isset($tokens[$stackPtr]['nested_parenthesis']) === true);

            $functionLine   = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['line'];
            $lineDifference = ($braceLine - $functionLine);

            if ($nestedFunction === true) {
                if ($lineDifference > 0) {
                    $error = 'Opening brace should be on the same line as the function keyword';
                    $phpcsFile->addError($error, $openBrace, 'SpacingAfterNested');
                }
            } else {
                if ($lineDifference === 0) {
                    $error = 'Opening brace should be on a new line';
                    $phpcsFile->addError($error, $openBrace, 'ContentBefore');
                    return;
                }

                if ($lineDifference > 1) {
                    $error = 'Opening brace should be on the line after the declaration; found %s blank line(s)';
                    $data  = array(($lineDifference - 1));
                    $phpcsFile->addError($error, $openBrace, 'SpacingBefore', $data);
                    return;
                }
            }//end if
        }//end if

    }//end process()


}//end class

?>
