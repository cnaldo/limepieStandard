<?php

class LimepieStandard_Sniffs_WhiteSpace_ReturnSpaceSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                   'CSS',
                                  );

    /**
     * If TRUE, whitespace rules are not checked for blank lines.
     *
     * Blank lines are those that contain only whitespace.
     *
     * @var boolean
     */
    public $ignoreBlankLines = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_RETURN,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        return;
        $tokens = $phpcsFile->getTokens();
        $prev     = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
 //           if($tokens[$prev+1]['line'] == 54) {
   //     pr(substr_count($tokens[$prev+1]['content'], "\n"));

$count = 0;
for($i=$prev;$i<$stackPtr;$i++){
     //   pr([$i,$tokens[$i]]);
    if($tokens[$i]['content'] == "\n") {
        $count ++;
    } else {
    }
}
//pr($count);
   //         }
        if($count == 2) {
        } else {
               $fix = $phpcsFile->addFixableError('Whitespace found at end of line', ($stackPtr - 1), 'EndLine');
        
        }

    }//end process()


}//end class
