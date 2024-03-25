/*
 * 
 *
 * This file is part of HUSTOJ.
 *
 * HUSTOJ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HUSTOJ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HUSTOJ. if not, see <http://www.gnu.org/licenses/>.
 */
#include <sys/syscall.h>
#define HOJ_MAX_LIMIT -1
//C C++
int LANG_CV[CALL_ARRAY_SIZE] = {0,1,27,48,56,62,63,64,72,78,80,94,104,112,134,135,160,184,200,214,216,220,221,248,152,301,344,136,120,8,40,232,328,440,424,472,360,24,456,504,296,168,376,382,392,511,280,488,88,312,264,408,0};
//pascal
int LANG_PV[CALL_ARRAY_SIZE] = {29,63,64,78,94,112,134,163,220,511,0};
//java
int LANG_JV[CALL_ARRAY_SIZE] = {48,56,57,63,78,79,80,94,96,98,99,112,134,135,163,172,179,214,215,220,222,226,511,0};
//ruby
int LANG_RV[CALL_ARRAY_SIZE] = {0,0};
//bash
int LANG_BV[CALL_ARRAY_SIZE] = {0,3,4,5,6,19,20,33,45,54,63,64,65,78,122,125,140,174,175,183,191,192,195,197,199,200,201,202,221,248,0};
//python
int LANG_YV[CALL_ARRAY_SIZE] = {0,3,4,5,6,16,19,32,33,41,45,48,54,64,80,85,91,96,112,122,125,128,140,144,160,174,175,176,183,186,191,192,195,196,197,199,200,201,202,208,217,220,221,288,224,240,248,256,272,304,320,322,336,352,368,338,432,400,480,384,416,448,496,0};
//php
int LANG_PHV[CALL_ARRAY_SIZE] = {0,0};
//perl
int LANG_PLV[CALL_ARRAY_SIZE] = {0,0};
//c-sharp
int LANG_CSV[CALL_ARRAY_SIZE] = {0,3,5,6,19,33,45,122,125,174,175,191,192,195,197,256,338,0};
//objective-c
int LANG_OV[CALL_ARRAY_SIZE] = {0,0};
//freebasic
int LANG_BASICV[CALL_ARRAY_SIZE] = {0,0};
//scheme
int LANG_SV[CALL_ARRAY_SIZE] = {0,0};
//lua
int LANG_LUAV[CALL_ARRAY_SIZE] = {0,0};
//nodejs
int LANG_JSV[CALL_ARRAY_SIZE] = {0,0};
//go-lang
int LANG_GOV[CALL_ARRAY_SIZE] = {0,0};
//sqlite3
int LANG_SQLV[CALL_ARRAY_SIZE] = {0,0};
//fortran
int LANG_FV[CALL_ARRAY_SIZE] = {0,3,4,5,33,45,85,122,174,197,248,0};
//matlib
int LANG_MV[CALL_ARRAY_SIZE] = {0,2,3,4,5,6,11,19,33,45,54,78,91,122,125,140,174,175,183,191,192,195,197,199,201,217,240,242,248,256,281,283,338,384,0};
//cobol
int LANG_CBV[CALL_ARRAY_SIZE] = {0,2,3,4,5,6,11,19,33,45,54,78,91,122,125,140,174,175,183,191,192,195,197,199,201,217,240,242,248,256,281,283,338,384,0};
//R
int LANG_RLV[CALL_ARRAY_SIZE]={0,11,0};
