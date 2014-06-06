<?php
/**
 * LimepieStandard_Sniffs_Classes_ClassFileNameSniff.
 * Tests that the file name and the name of the class contained within the file
 * match.
 */

 //파일네임과 클레스네임의 일치
class LimepieStandard_Sniffs_Classes_ClassFileNameSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $fullPath = basename($phpcsFile->getFilename());
        $fileName = substr($fullPath, 0, strrpos($fullPath, '.'));
        if ($fileName === '') {
            // No filename probably means STDIN, so we can't do this check.
            return;
        }

        $tokens  = $phpcsFile->getTokens();
        $decName = $phpcsFile->findNext(T_STRING, $stackPtr);


        if ($tokens[$decName]['content'] !== $fileName) {
            $error = '%s name doesn\'t match filename; expected "%s %s"';
            $data  = array(
                      ucfirst($tokens[$stackPtr]['content']),
                      $tokens[$stackPtr]['content'],
                      $fileName,
                     );
            $phpcsFile->addError($error, $stackPtr, 'NoMatch', $data);
        }

    }//end process()


}//end class

?>
