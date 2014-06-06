<?php
/**
 * LimepieStandard2_Sniffs_Namespaces_NamespaceDeclarationSniff.
 */
class LimepieStandard_Sniffs_Namespaces_NamespaceDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_NAMESPACE);

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
        $tokens = $phpcsFile->getTokens();


        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {

                if($tokens[$i]['type'] == 'T_STRING') {

                    $valid = PHP_CodeSniffer::isCamelCaps($tokens[$i]['content'], false, true, false); // 2번째 파라메터가 true이면 PascalCase
                    if ($valid === false) {
                        $type  = 'namespace';//ucfirst($tokens[$i]['content']);
                        $error = '%s path "%s" is not in camel caps format';
                        $data  = array(
                                  $type,
                                  $tokens[$i]['content'],
                                 );
                        $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
                    }
                
                }
                continue;
            }

            break;
        }

        // The $i var now points to the first token on the line after the
        // namespace declaration, which must be a blank line.
        $next = $phpcsFile->findNext(T_WHITESPACE, $i, $phpcsFile->numTokens, true);

        $prev = $phpcsFile->findNext(T_WHITESPACE, 0, $phpcsFile->numTokens, false);


  if($tokens[$prev]['line'] == 1) {
            $error = 'There must be one blank line after the opentag declaration';
            $phpcsFile->addError($error, $stackPtr, 'OpenTagAfter');        
  }


        if ($tokens[$stackPtr]['line'] != 3) {
            $error = 'namespace는 2번째 라인에 정의';
            $phpcsFile->addError($error, $stackPtr, 'BlankLineBefore');        
        }

        if ( $tokens[$next]['line'] === $tokens[$i]['line']) {
         $error = 'There must be one blank line after the namespace declaration';
          $phpcsFile->addError($error, $stackPtr, 'BlankLineAfter');
        }

    }//end process()


}//end class


?>
