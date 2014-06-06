<?php
/**
 * LimepieStandard_Sniffs_Namespaces_UseDeclarationSniff.
 */
class LimepieStandard_Sniffs_Namespaces_UseDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_USE);

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
        if ($this->_shouldIgnoreUse($phpcsFile, $stackPtr) === true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();


        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {



                if($tokens[$i]['type'] == 'T_STRING') {
                    $valid = PHP_CodeSniffer::isCamelCaps($tokens[$i]['content'], false, true, false); // 2번째 파라메터가 true이면 PascalCase
                    if ($valid === false) {
                        $type  = 'use';//ucfirst($tokens[$i]['content']);
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

        $lastLine = $tokens[$stackPtr]['line'];
        while ($tokens[$stackPtr-1]['code'] === T_WHITESPACE) {
             $stackPtr--;
        }

        $lastCodeLine = $tokens[$stackPtr]['line'];
        $blankLines   = ($lastLine - $lastCodeLine);
        if ($blankLines > 2) {
            $error = 'Expected 1 blank line at end of file; %s found';
            $data  = array($blankLines - 1);
            $phpcsFile->addError($error, $stackPtr, 'TooMany', $data);
        }

    
        // Only one USE declaration allowed per statement.
        $next = $phpcsFile->findNext(array(T_COMMA, T_SEMICOLON), ($stackPtr + 1));
        if ($tokens[$next]['code'] === T_COMMA) {
            $error = 'There must be one USE keyword per declaration';
            $phpcsFile->addError($error, $stackPtr, 'MultipleDeclarations');
        }

        // Make sure this USE comes after the first namespace declaration.
        $prev = $phpcsFile->findPrevious(T_NAMESPACE, ($stackPtr - 1));
        if ($prev !== false) {
            $first = $phpcsFile->findNext(T_NAMESPACE, 1);
            if ($prev !== $first) {
                $error = 'USE declarations must go after the first namespace declaration';
                $phpcsFile->addError($error, $stackPtr, 'UseAfterNamespace');
            }
        }

        // Only interested in the last USE statement from here onwards.
        $nextUse = $phpcsFile->findNext(T_USE, ($stackPtr + 1));
        while ($this->_shouldIgnoreUse($phpcsFile, $nextUse) === true) {
            $nextUse = $phpcsFile->findNext(T_USE, ($nextUse + 1));

            if ($nextUse === false) {
                break;
            }
        }

        if ($nextUse !== false) {
            return;
        }

        $end  = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
        $next = $phpcsFile->findNext(T_WHITESPACE, ($end + 1), null, true);
        $diff = ($tokens[$next]['line'] - $tokens[$end]['line'] - 1);
        if ($diff !== 1) {
            if ($diff < 0) {
                $diff = 0;
            }

            $error = 'There must be one blank line after the last USE statement; %s found;';
            $data  = array($diff);
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterLastUse', $data);
        }

    }//end process()


    /**
     * Check if this use statement is part of the namespace block.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    private function _shouldIgnoreUse(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return true;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, array(T_CLASS, T_TRAIT)) === true) {
            return true;
        }

        return false;

    }//end _shouldIgnoreUse()


}//end class


?>
