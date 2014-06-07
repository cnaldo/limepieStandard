<?php

/**
 * LimepieStandard_Sniffs_WhiteSpace_ControlStructureSpacingSniff.
 *
 * Checks that control structures have the correct spacing around brackets.
 */
class LimepieStandard_Sniffs_WhiteSpace_ControlStructureSpacingSniff implements PHP_CodeSniffer_Sniff
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
        return array(
                T_IF,
                T_WHILE,
                T_FOREACH,
                T_FOR,
                T_SWITCH,
                T_DO,
                T_ELSE,
                T_ELSEIF,
               );

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


if(isset($tokens[$stackPtr]['scope_closer']) == false) {
   // pr($tokens[$stackPtr]);
    return;
}
$prev = $phpcsFile->findPrevious(
    T_WHITESPACE,
    ($stackPtr - 1),
    null,
    true
);

$prevSpace = $phpcsFile->findNext(
    T_WHITESPACE,
    ($stackPtr),
    null,
    true
);

    $scopeClose = $tokens[$stackPtr]['scope_closer'];

$next = $phpcsFile->findNext(
    T_CLOSE_CURLY_BRACKET,
    ($scopeClose +1),
    null,
    true
);


$nextSpace = $phpcsFile->findNext(
    T_WHITESPACE,
    ($next +1),
    null,
    true
);

$prevSpaceNumber = $tokens[$prevSpace]['line'] - $tokens[$prev]['line'] - 1;

if(
    $tokens[$prev]['type'] == 'T_CLOSE_CURLY_BRACKET' && 
    $tokens[$prevSpace]['type'] == 'T_ELSEIF' &&
    $tokens[$prev]['line'] != $tokens[$prevSpace]['line']
) {

} else if($prevSpaceNumber == -1) {

} else if($prevSpaceNumber == 1) {

} else {
      $error = '한줄 필요-1, found :'. $prevSpaceNumber;
      $phpcsFile->addError($error, $stackPtr, 'SpacingPrevOpenBrace');

}

$nextSpaceNumber = $tokens[$nextSpace]['line'] - $tokens[$next]['line'] - 1;
if(
    $tokens[$next]['type'] == 'T_WHITESPACE' && 
    $tokens[$nextSpace]['type'] == 'T_ELSEIF' &&
    $tokens[$next]['line'] != $tokens[$nextSpace]['line']
) {

} else if($nextSpaceNumber == -1) {
    if($tokens[$next-1]['line'] == $tokens[$nextSpace]['line']
    && $tokens[$nextSpace]['type'] != "T_ELSEIF"
    && $tokens[$nextSpace]['type'] != "T_ELSE"
        
    ) {
          $error = '}에 바로 쓰지 마세요2. 두 줄 필요, found :0';
          $phpcsFile->addError($error, $nextSpace, 'SpacingAfterCloseBrace');

    }

} else if($nextSpaceNumber == 1) {

} else if($tokens[$next-1]['line'] == $tokens[$nextSpace]['line']
&& $tokens[$nextSpace]['type'] != "T_ELSEIF"
&& $tokens[$nextSpace]['type'] != "T_ELSE"
) {
      $error = '}에 바로 쓰지 마세요. 두 줄 필요, found :0';
      $phpcsFile->addError($error, $nextSpace, 'SpacingAfterCloseBrace');
}else {
    

      $error = '한줄 필요+1, found :'. $nextSpaceNumber;
      $phpcsFile->addError($error, $nextSpace, 'SpacingAfterCloseBrace');

}



return;
        if (isset($tokens[$stackPtr]['parenthesis_opener']) === true) {
            $parenOpener = $tokens[$stackPtr]['parenthesis_opener'];
            $parenCloser = $tokens[$stackPtr]['parenthesis_closer'];
            if ($tokens[($parenOpener + 1)]['code'] === T_WHITESPACE) {
                $gap   = strlen($tokens[($parenOpener + 1)]['content']);
                $error = 'Expected 0 spaces after opening bracket; %s found';
                $data  = array($gap);
                $phpcsFile->addError($error, ($parenOpener + 1), 'SpacingAfterOpenBrace', $data);
            }

            if ($tokens[$parenOpener]['line'] === $tokens[$parenCloser]['line']
                && $tokens[($parenCloser - 1)]['code'] === T_WHITESPACE
            ) {
                $gap   = strlen($tokens[($parenCloser - 1)]['content']);
                $error = 'Expected 0 spaces before closing bracket; %s found';
                $data  = array($gap);
                $phpcsFile->addError($error, ($parenCloser - 1), 'SpaceBeforeCloseBrace', $data);
            }
        }//end if

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'];
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];

        $firstContent = $phpcsFile->findNext(
            T_WHITESPACE,
            ($scopeOpener + 1),
            null,
            true
        );

        if ($tokens[$firstContent]['line'] !== ($tokens[$scopeOpener]['line'] + 1)) {
            $error = 'Blank line found at start of control structure';
            $phpcsFile->addError($error, $scopeOpener, 'SpacingBeforeOpen');
        }

        if ($firstContent !== $scopeCloser) {
            $lastContent = $phpcsFile->findPrevious(
                T_WHITESPACE,
                ($scopeCloser - 1),
                null,
                true
            );

            if ($tokens[$lastContent]['line'] !== ($tokens[$scopeCloser]['line'] - 1)) {
                $errorToken = $scopeCloser;
                for ($i = ($scopeCloser - 1); $i > $lastContent; $i--) {
                    if ($tokens[$i]['line'] < $tokens[$scopeCloser]['line']) {
                        $errorToken = $i;
                        break;
                    }
                }

                $error = 'Blank line found at end of control structure';
                $phpcsFile->addError($error, $errorToken, 'SpacingAfterClose');
            }
        }

        $trailingContent = $phpcsFile->findNext(
            T_WHITESPACE,
            ($scopeCloser + 1),
            null,
            true
        );

        if ($tokens[$trailingContent]['code'] === T_COMMENT) {
            // Special exception for code where the comment about
            // and ELSE or ELSEIF is written between the control structures.
            $nextCode = $phpcsFile->findNext(
                PHP_CodeSniffer_Tokens::$emptyTokens,
                ($scopeCloser + 1),
                null,
                true
            );

            if ($tokens[$nextCode]['code'] === T_ELSE
                || $tokens[$nextCode]['code'] === T_ELSEIF
            ) {
                $trailingContent = $nextCode;
            }
        }//end if

        if ($tokens[$trailingContent]['code'] === T_ELSE) {
            if ($tokens[$stackPtr]['code'] === T_IF) {
                // IF with ELSE.
             //   return;
            }
        }

        if ($tokens[$trailingContent]['code'] === T_CLOSE_TAG) {
            // At the end of the script or embedded code.
            return;
        }

        if (isset($tokens[$trailingContent]['scope_condition']) === true
            && $tokens[$trailingContent]['scope_condition'] !== $trailingContent
        ) {
            // Another control structure's closing brace.
            $owner = $tokens[$trailingContent]['scope_condition'];
            if ($tokens[$owner]['code'] === T_FUNCTION) {
                // The next content is the closing brace of a function
                // so normal function rules apply and we can ignore it.
                return;
            }

            if ($tokens[$trailingContent]['line'] !== ($tokens[$scopeCloser]['line'] + 1)) {
               // $error = 'Blank line found after control structure';
               // $phpcsFile->addError($error, $scopeCloser, 'LineAfterClose');
            } else {
             //   echo 'a';
            }
        } else if ($tokens[$trailingContent]['code'] !== T_ELSE // else 뒤에 빈줄 넣는것 삭제
            && $tokens[$trailingContent]['code'] !== T_ELSEIF
            && $tokens[$trailingContent]['line'] === ($tokens[$scopeCloser]['line'] + 1)
        ) {
           // $error = 'No blank line found after control structure';
           // $phpcsFile->addError($error, $scopeCloser, 'NoLineAfterClose');
        } else {
          //  echo 'b';
        }


    }//end process()


}//end class

?>
