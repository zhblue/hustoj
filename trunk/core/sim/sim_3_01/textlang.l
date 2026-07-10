%{
/*	This file is part of the software similarity tester SIM.
	Written by Dick Grune, Vrije Universiteit, Amsterdam.
	$Id: textlang.l,v 1.21 2016-05-13 19:00:55 dick Exp $
*/

/*
	Text front end for the similarity tester.
*/

#include	"sim.h"
#include	"token.h"
#include	"idf.h"

#include	"lex.h"
#include	"lang.h"
#include	"language.h"

/* compatible with gcc 13 or 14 */
#include    "options.h"

/* General language front end data */
Token lex_token;
size_t lex_nl_cnt;
size_t lex_tk_cnt;
size_t lex_non_ascii_cnt;

/* Language-dependent code */

const char *Subject = "text";

void
Init_Language(void) {
	if (is_set_option('f') || is_set_option('F'))
		fatal("options -f or -F not applicable in sim_text");
	Token_Name = "word";
	Threshold_Percentage = 20;
}

/*ARGSUSED*/
int
May_Be_Start_Of_Run(Token tk) {
	/* any token is acceptable */
	return 1;
}

/*ARGSUSED*/
size_t
Best_Run_Size(const Token *str, size_t size) {
	/* any run size is acceptable */
	return size;
}

static Token
word2token(char *word) {
	/* ignore case */
	lower_case(word);
	return idf_hashed(word);
}

%}

%option	noyywrap

WordElem	([a-zA-Z0-9\200-\377])
TightWord	({WordElem}+)

NonWordElem	([^a-zA-Z0-9\200-\377])
LooseElem	({WordElem}(" "))
SpacedWord	({LooseElem}+{WordElem})

%%

{TightWord}	{
		return_tk(word2token(yytext));
	}

{SpacedWord}/{NonWordElem}	{
		/* the / operator works at the top level only */
		return_tk(word2token(yytext));
	}


\n	{				/* count newlines */
		return_eol();
	}

.	{			/* ignore the rest */
	}

%%

/* More language-dependent code */

void
yystart(void) {
	BEGIN INITIAL;
}
