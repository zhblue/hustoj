/*
 * It is strictly recommended to include "testlib.h" before any other include
 * in your code. In this case testlib overrides compiler specific "random()".
 *
 * If you can't compile your code and compiler outputs something about
 * ambiguous call of "random_shuffle", "rand" or "srand" it means that
 * you shouldn't use them. Use "shuffle", and "rnd.next()" instead of them
 * because these calls produce stable result for any C++ compiler. Read
 * sample generator sources for clarification.
 *
 * Please read the documentation for class "random_t" and use "rnd" instance in
 * generators. Probably, these sample calls will be useful for you:
 *              rnd.next(); rnd.next(100); rnd.next(1, 2);
 *              rnd.next(3.14); rnd.next("[a-z]{1,100}").
 *
 * Also read about wnext() to generate off-center random distribution.
 *
 * See https://github.com/MikeMirzayanov/testlib/ to get latest version or bug tracker.
 */

#ifndef _TESTLIB_H_
#define _TESTLIB_H_

/*
 * Copyright (c) 2005-2023
 */

#define VERSION "0.9.41"

/*
 * Mike Mirzayanov
 *
 * This material is provided "as is", with absolutely no warranty expressed
 * or implied. Any use is at your own risk.
 *
 * Permission to use or copy this software for any purpose is hereby granted
 * without fee, provided the above notices are retained on all copies.
 * Permission to modify the code and to distribute modified code is granted,
 * provided the above notices are retained, and a notice that the code was
 * modified is included with the above copyright notice.
 *
 */

/* NOTE: This file contains testlib library for C++.
 *
 *   Check, using testlib running format:
 *     check.exe <Input_File> <Output_File> <Answer_File> [<Result_File> [-appes]],
 *   If result file is specified it will contain results.
 *
 *   Validator, using testlib running format:
 *     validator.exe < input.txt,
 *   It will return non-zero exit code and writes message to standard output.
 *
 *   Generator, using testlib running format:
 *     gen.exe [parameter-1] [parameter-2] [... paramerter-n]
 *   You can write generated test(s) into standard output or into the file(s).
 *
 *   Interactor, using testlib running format:
 *     interactor.exe <Input_File> <Output_File> [<Answer_File> [<Result_File> [-appes]]],
 *   Reads test from inf (mapped to args[1]), writes result to tout (mapped to argv[2],
 *   can be judged by checker later), reads program output from ouf (mapped to stdin),
 *   writes output to program via stdout (use cout, printf, etc).
 */

const char *latestFeatures[] = {
        "Use setAppesModeEncoding to change xml encoding from windows-1251 to other",
        "rnd.any/wany use distance/advance instead of -/+: now they support sets/multisets",
        "Use syntax `int t = inf.readInt(1, 3, \"~t\");` to skip the lower bound check. Tildes can be used on either side or both: ~t, t~, ~t~",
        "Supported EJUDGE support in registerTestlibCmd",
        "Supported '--testMarkupFileName fn' and '--testCase tc/--testCaseFileName fn' for validators",
        "Added opt defaults via opt<T>(key/index, default_val); check unused opts when using has_opt or default opt (turn off this check with suppressEnsureNoUnusedOpt()).",
        "For checker added --group and --testset command line params (like for validator), use checker.group() or checker.testset() to get values",
        "Added quitpi(points_info, message) function to return with _points exit code 7 and given points_info",
        "rnd.partition(size, sum[, min_part=1]) returns random (unsorted) partition which is a representation of the given `sum` as a sum of `size` positive integers (or >=min_part if specified)",
        "rnd.distinct(size, n) and rnd.distinct(size, from, to)",
        "opt<bool>(\"some_missing_key\") returns false now",
        "has_opt(key)",
        "Abort validator on validator.testset()/validator.group() if registered without using command line",
        "Print integer range violations in a human readable way like `violates the range [1, 10^9]`",
        "Opts supported: use them like n = opt<int>(\"n\"), in a command line you can use an exponential notation",
        "Reformatted",
        "Use setTestCase(i) or unsetTestCase() to support test cases (you can use it in any type of program: generator, interactor, validator or checker)",
        "Fixed issue #87: readStrictDouble accepts \"-0.00\"",
        "Fixed issue #83: added InStream::quitif(condition, ...)",
        "Fixed issue #79: fixed missed guard against repeated header include",
        "Fixed issue #80: fixed UB in case of huge quitf message",
        "Fixed issue #84: added readXs(size, indexBase = 1)",
        "Fixed stringstream repeated usage issue",
        "Fixed compilation in g++ (for std=c++03)",
        "Batch of println functions (support collections, iterator ranges)",
        "Introduced rnd.perm(size, first = 0) to generate a `first`-indexed permutation",
        "Allow any whitespace in readInts-like functions for non-validators",
        "Ignore 4+ command line arguments ifdef EJUDGE",
        "Speed up of vtos",
        "Show line number in validators in case of incorrect format",
        "Truncate huge checker/validator/interactor message",
        "Fixed issue with readTokenTo of very long tokens, now aborts with _pe/_fail depending of a stream type",
        "Introduced InStream::ensure/ensuref checking a condition, returns wa/fail depending of a stream type",
        "Fixed compilation in VS 2015+",
        "Introduced space-separated read functions: readWords/readTokens, multilines read functions: readStrings/readLines",
        "Introduced space-separated read functions: readInts/readIntegers/readLongs/readUnsignedLongs/readDoubles/readReals/readStrictDoubles/readStrictReals",
        "Introduced split/tokenize functions to separate string by given char",
        "Introduced InStream::readUnsignedLong and InStream::readLong with unsigned long long parameters",
        "Supported --testOverviewLogFileName for validator: bounds hits + features",
        "Fixed UB (sequence points) in random_t",
        "POINTS_EXIT_CODE returned back to 7 (instead of 0)",
        "Removed disable buffers for interactive problems, because it works unexpectedly in wine",
        "InStream over string: constructor of InStream from base InStream to inherit policies and std::string",
        "Added expectedButFound quit function, examples: expectedButFound(_wa, 10, 20), expectedButFound(_fail, ja, pa, \"[n=%d,m=%d]\", n, m)",
        "Fixed incorrect interval parsing in patterns",
        "Use registerGen(argc, argv, 1) to develop new generator, use registerGen(argc, argv, 0) to compile old generators (originally created for testlib under 0.8.7)",
        "Introduced disableFinalizeGuard() to switch off finalization checkings",
        "Use join() functions to format a range of items as a single string (separated by spaces or other separators)",
        "Use -DENABLE_UNEXPECTED_EOF to enable special exit code (by default, 8) in case of unexpected eof. It is good idea to use it in interactors",
        "Use -DUSE_RND_AS_BEFORE_087 to compile in compatibility mode with random behavior of versions before 0.8.7",
        "Fixed bug with nan in stringToDouble",
        "Fixed issue around overloads for size_t on x64",
        "Added attribute 'points' to the XML output in case of result=_points",
        "Exit codes can be customized via macros, e.g. -DPE_EXIT_CODE=14",
        "Introduced InStream function readWordTo/readTokenTo/readStringTo/readLineTo for faster reading",
        "Introduced global functions: format(), englishEnding(), upperCase(), lowerCase(), compress()",
        "Manual buffer in InStreams, some IO speed improvements",
        "Introduced quitif(bool, const char* pattern, ...) which delegates to quitf() in case of first argument is true",
        "Introduced guard against missed quitf() in checker or readEof() in validators",
        "Supported readStrictReal/readStrictDouble - to use in validators to check strictly float numbers",
        "Supported registerInteraction(argc, argv)",
        "Print checker message to the stderr instead of stdout",
        "Supported TResult _points to output calculated score, use quitp(...) functions",
        "Fixed to be compilable on Mac",
        "PC_BASE_EXIT_CODE=50 in case of defined TESTSYS",
        "Fixed issues 19-21, added __attribute__ format printf",
        "Some bug fixes",
        "ouf.readInt(1, 100) and similar calls return WA",
        "Modified random_t to avoid integer overflow",
        "Truncated checker output [patch by Stepan Gatilov]",
        "Renamed class random -> class random_t",
        "Supported name parameter for read-and-validation methods, like readInt(1, 2, \"n\")",
        "Fixed bug in readDouble()",
        "Improved ensuref(), fixed nextLine to work in case of EOF, added startTest()",
        "Supported \"partially correct\", example: quitf(_pc(13), \"result=%d\", result)",
        "Added shuffle(begin, end), use it instead of random_shuffle(begin, end)",
        "Added readLine(const string& ptrn), fixed the logic of readLine() in the validation mode",
        "Package extended with samples of generators and validators",
        "Written the documentation for classes and public methods in testlib.h",
        "Implemented random routine to support generators, use registerGen() to switch it on",
        "Implemented strict mode to validate tests, use registerValidation() to switch it on",
        "Now ncmp.cpp and wcmp.cpp are return WA if answer is suffix or prefix of the output",
        "Added InStream::readLong() and removed InStream::readLongint()",
        "Now no footer added to each report by default (use directive FOOTER to switch on)",
        "Now every checker has a name, use setName(const char* format, ...) to set it",
        "Now it is compatible with TTS (by Kittens Computing)",
        "Added \'ensure(condition, message = \"\")\' feature, it works like assert()",
        "Fixed compatibility with MS C++ 7.1",
        "Added footer with exit code information",
        "Added compatibility with EJUDGE (compile with EJUDGE directive)",
        "Added compatibility with Contester (compile with CONTESTER directive)"
};

#ifdef _MSC_VER
#define _CRT_SECURE_NO_DEPRECATE
#define _CRT_SECURE_NO_WARNINGS
#define _CRT_NO_VA_START_VALIDATION
#endif

/* Overrides random() for Borland C++. */
#define random __random_deprecated
#include <stdlib.h>
#include <cstdlib>
#include <climits>
#include <algorithm>
#undef random

#include <cstdio>
#include <cctype>
#include <string>
#include <vector>
#include <map>
#include <set>
#include <cmath>
#include <iterator>
#include <iostream>
#include <sstream>
#include <fstream>
#include <cstring>
#include <limits>
#include <stdarg.h>
#include <fcntl.h>
#include <functional>
#include <cstdint>

#ifdef TESTLIB_THROW_EXIT_EXCEPTION_INSTEAD_OF_EXIT
#   include <exception>
#endif

#if (_WIN32 || __WIN32__ || __WIN32 || _WIN64 || __WIN64__ || __WIN64 || WINNT || __WINNT || __WINNT__ || __CYGWIN__)
#   if !defined(_MSC_VER) || _MSC_VER > 1400
#       define NOMINMAX 1
#       include <windows.h>
#   else
#       define WORD unsigned short
#       include <unistd.h>
#   endif
#   include <io.h>
#   define ON_WINDOWS
#   if defined(_MSC_VER) && _MSC_VER > 1400
#       pragma warning( disable : 4127 )
#       pragma warning( disable : 4146 )
#       pragma warning( disable : 4458 )
#   endif
#else
#   define WORD unsigned short
#   include <unistd.h>
#endif

#if defined(FOR_WINDOWS) && defined(FOR_LINUX)
#error Only one target system is allowed
#endif

#ifndef LLONG_MIN
#define LLONG_MIN   (-9223372036854775807LL - 1)
#endif

#ifndef ULLONG_MAX
#define ULLONG_MAX   (18446744073709551615)
#endif

#define LF ((char)10)
#define CR ((char)13)
#define TAB ((char)9)
#define SPACE ((char)' ')
#define EOFC (255)

#ifndef OK_EXIT_CODE
#   ifdef CONTESTER
#       define OK_EXIT_CODE 0xAC
#   else
#       define OK_EXIT_CODE 0
#   endif
#endif

#ifndef WA_EXIT_CODE
#   ifdef EJUDGE
#       define WA_EXIT_CODE 5
#   elif defined(CONTESTER)
#       define WA_EXIT_CODE 0xAB
#   else
#       define WA_EXIT_CODE 1
#   endif
#endif

#ifndef PE_EXIT_CODE
#   ifdef EJUDGE
#       define PE_EXIT_CODE 4
#   elif defined(CONTESTER)
#       define PE_EXIT_CODE 0xAA
#   else
#       define PE_EXIT_CODE 2
#   endif
#endif

#ifndef FAIL_EXIT_CODE
#   ifdef EJUDGE
#       define FAIL_EXIT_CODE 6
#   elif defined(CONTESTER)
#       define FAIL_EXIT_CODE 0xA3
#   else
#       define FAIL_EXIT_CODE 3
#   endif
#endif

#ifndef DIRT_EXIT_CODE
#   ifdef EJUDGE
#       define DIRT_EXIT_CODE 6
#   else
#       define DIRT_EXIT_CODE 4
#   endif
#endif

#ifndef POINTS_EXIT_CODE
#   define POINTS_EXIT_CODE 7
#endif

#ifndef UNEXPECTED_EOF_EXIT_CODE
#   define UNEXPECTED_EOF_EXIT_CODE 8
#endif

#ifndef PC_BASE_EXIT_CODE
#   ifdef TESTSYS
#       define PC_BASE_EXIT_CODE 50
#   else
#       define PC_BASE_EXIT_CODE 0
#   endif
#endif

#ifdef __GNUC__
#    define __TESTLIB_STATIC_ASSERT(condition) typedef void* __testlib_static_assert_type[(condition) ? 1 : -1] __attribute__((unused))
#else
#    define __TESTLIB_STATIC_ASSERT(condition) typedef void* __testlib_static_assert_type[(condition) ? 1 : -1]
#endif

#ifdef ON_WINDOWS
#define I64 "%I64d"
#define U64 "%I64u"
#else
#define I64 "%lld"
#define U64 "%llu"
#endif

#ifdef _MSC_VER
#   define NORETURN __declspec(noreturn)
#elif defined __GNUC__
#   define NORETURN __attribute__ ((noreturn))
#else
#   define NORETURN
#endif

static char __testlib_format_buffer[16777216];
static int __testlib_format_buffer_usage_count = 0;

#define FMT_TO_RESULT(fmt, cstr, result)  std::string result;                              \
            if (__testlib_format_buffer_usage_count != 0)                                  \
                __testlib_fail("FMT_TO_RESULT::__testlib_format_buffer_usage_count != 0"); \
            __testlib_format_buffer_usage_count++;                                         \
            va_list ap;                                                                    \
            va_start(ap, fmt);                                                             \
            vsnprintf(__testlib_format_buffer, sizeof(__testlib_format_buffer), cstr, ap); \
            va_end(ap);                                                                    \
            __testlib_format_buffer[sizeof(__testlib_format_buffer) - 1] = 0;              \
            result = std::string(__testlib_format_buffer);                                 \
            __testlib_format_buffer_usage_count--;                                         \

const long long __TESTLIB_LONGLONG_MAX = 9223372036854775807LL;
const int __TESTLIB_MAX_TEST_CASE = 1073741823;

int __testlib_exitCode;

bool __testlib_hasTestCase;
int __testlib_testCase = -1;

void setTestCase(int testCase);

void unsetTestCase() {
    __testlib_hasTestCase = false;
    __testlib_testCase = -1;
}

NORETURN static void __testlib_fail(const std::string &message);

template<typename T>
#ifdef __GNUC__
__attribute__((const))
#endif
static inline T __testlib_abs(const T &x) {
    return x > 0 ? x : -x;
}

template<typename T>
#ifdef __GNUC__
__attribute__((const))
#endif
static inline T __testlib_min(const T &a, const T &b) {
    return a < b ? a : b;
}

template<typename T>
#ifdef __GNUC__
__attribute__((const))
#endif
static inline T __testlib_max(const T &a, const T &b) {
    return a > b ? a : b;
}

template<typename T>
#ifdef __GNUC__
__attribute__((const))
#endif
static inline T __testlib_crop(T value, T a, T b) {
    return __testlib_min(__testlib_max(value, a), --b);
}

#ifdef __GNUC__
__attribute__((const))
#endif
static inline double __testlib_crop(double value, double a, double b) {
    value = __testlib_min(__testlib_max(value, a), b);
    if (value >= b)
        value = std::nexttoward(b, a);
    return value;
}

static bool __testlib_prelimIsNaN(double r) {
    volatile double ra = r;
#ifndef __BORLANDC__
    return ((ra != ra) == true) && ((ra == ra) == false) && ((1.0 > ra) == false) && ((1.0 < ra) == false);
#else
    return std::_isnan(ra);
#endif
}

#ifdef __GNUC__
__attribute__((const))
#endif
static std::string removeDoubleTrailingZeroes(std::string value) {
    while (!value.empty() && value[value.length() - 1] == '0' && value.find('.') != std::string::npos)
        value = value.substr(0, value.length() - 1);
    if (!value.empty() && value[value.length() - 1] == '.')
        return value + '0';
    else
        return value;
}

#ifdef __GNUC__
__attribute__((const))
#endif
inline std::string upperCase(std::string s) {
    for (size_t i = 0; i < s.length(); i++)
        if ('a' <= s[i] && s[i] <= 'z')
            s[i] = char(s[i] - 'a' + 'A');
    return s;
}

#ifdef __GNUC__
__attribute__((const))
#endif
inline std::string lowerCase(std::string s) {
    for (size_t i = 0; i < s.length(); i++)
        if ('A' <= s[i] && s[i] <= 'Z')
            s[i] = char(s[i] - 'A' + 'a');
    return s;
}

#ifdef __GNUC__
__attribute__ ((format (printf, 1, 2)))
#endif
std::string format(const char *fmt, ...) {
    FMT_TO_RESULT(fmt, fmt, result);
    return result;
}

std::string format(const std::string fmt, ...) {
    FMT_TO_RESULT(fmt, fmt.c_str(), result);
    return result;
}

#ifdef __GNUC__
__attribute__((const))
#endif
static std::string __testlib_part(const std::string &s);

static bool __testlib_isNaN(double r) {
    __TESTLIB_STATIC_ASSERT(sizeof(double) == sizeof(long long));
    volatile double ra = r;
    long long llr1, llr2;
    std::memcpy((void *) &llr1, (void *) &ra, sizeof(double));
    ra = -ra;
    std::memcpy((void *) &llr2, (void *) &ra, sizeof(double));
    long long llnan = 0xFFF8000000000000LL;
    return __testlib_prelimIsNaN(r) || llnan == llr1 || llnan == llr2;
}

static double __testlib_nan() {
    __TESTLIB_STATIC_ASSERT(sizeof(double) == sizeof(long long));
#ifndef NAN
    long long llnan = 0xFFF8000000000000LL;
    double nan;
    std::memcpy(&nan, &llnan, sizeof(double));
    return nan;
#else
    return NAN;
#endif
}

static bool __testlib_isInfinite(double r) {
    volatile double ra = r;
    return (ra > 1E300 || ra < -1E300);
}

#ifdef __GNUC__
__attribute__((const))
#endif
inline bool doubleCompare(double expected, double result, double MAX_DOUBLE_ERROR) {
    MAX_DOUBLE_ERROR += 1E-15;
    if (__testlib_isNaN(expected)) {
        return __testlib_isNaN(result);
    } else if (__testlib_isInfinite(expected)) {
        if (expected > 0) {
            return result > 0 && __testlib_isInfinite(result);
        } else {
            return result < 0 && __testlib_isInfinite(result);
        }
    } else if (__testlib_isNaN(result) || __testlib_isInfinite(result)) {
        return false;
    } else if (__testlib_abs(result - expected) <= MAX_DOUBLE_ERROR) {
        return true;
    } else {
        double minv = __testlib_min(expected * (1.0 - MAX_DOUBLE_ERROR),
                                    expected * (1.0 + MAX_DOUBLE_ERROR));
        double maxv = __testlib_max(expected * (1.0 - MAX_DOUBLE_ERROR),
                                    expected * (1.0 + MAX_DOUBLE_ERROR));
        return result >= minv && result <= maxv;
    }
}

#ifdef __GNUC__
__attribute__((const))
#endif
inline double doubleDelta(double expected, double result) {
    double absolute = __testlib_abs(result - expected);

    if (__testlib_abs(expected) > 1E-9) {
        double relative = __testlib_abs(absolute / expected);
        return __testlib_min(absolute, relative);
    } else
        return absolute;
}

/** It does nothing on non-windows and files differ from stdin/stdout/stderr. */
static void __testlib_set_binary(std::FILE *file) {
    if (NULL != file) {
#ifdef ON_WINDOWS
#   ifdef _O_BINARY
        if (stdin == file)
#       ifdef STDIN_FILENO
                return void(_setmode(STDIN_FILENO, _O_BINARY));
#       else
                return void(_setmode(_fileno(stdin), _O_BINARY));
#       endif
        if (stdout == file)
#       ifdef STDOUT_FILENO
                return void(_setmode(STDOUT_FILENO, _O_BINARY));
#       else
                return void(_setmode(_fileno(stdout), _O_BINARY));
#       endif
        if (stderr == file)
#       ifdef STDERR_FILENO
                return void(_setmode(STDERR_FILENO, _O_BINARY));
#       else
                return void(_setmode(_fileno(stderr), _O_BINARY));
#       endif
#   elif O_BINARY
        if (stdin == file)
#       ifdef STDIN_FILENO
                return void(setmode(STDIN_FILENO, O_BINARY));
#       else
                return void(setmode(fileno(stdin), O_BINARY));
#       endif
        if (stdout == file)
#       ifdef STDOUT_FILENO
                return void(setmode(STDOUT_FILENO, O_BINARY));
#       else
                return void(setmode(fileno(stdout), O_BINARY));
#       endif
        if (stderr == file)
#       ifdef STDERR_FILENO
                return void(setmode(STDERR_FILENO, O_BINARY));
#       else
                return void(setmode(fileno(stderr), O_BINARY));
#       endif
#   endif
#endif
    }
}

#if __cplusplus > 199711L || defined(_MSC_VER)
template<typename T>
#ifdef __GNUC__
__attribute__((const))
#endif
static std::string vtos(const T &t, std::true_type) {
    if (t == 0)
        return "0";
    else {
        T n(t);
        bool negative = n < 0;
        std::string s;
        while (n != 0) {
            T digit = n % 10;
            if (digit < 0)
                digit = -digit;
            s += char('0' + digit);
            n /= 10;
        }
        std::reverse(s.begin(), s.end());
        return negative ? "-" + s : s;
    }
}

template<typename T>
static std::string vtos(const T &t, std::false_type) {
    std::string s;
    static std::stringstream ss;
    ss.str(std::string());
    ss.clear();
    ss << t;
    ss >> s;
    return s;
}

template<typename T>
static std::string vtos(const T &t) {
    return vtos(t, std::is_integral<T>());
}

/* signed case. */
template<typename T>
static std::string toHumanReadableString(const T &n, std::false_type) {
    if (n == 0)
        return vtos(n);
    int trailingZeroCount = 0;
    T n_ = n;
    while (n_ % 10 == 0)
        n_ /= 10, trailingZeroCount++;
    if (trailingZeroCount >= 7) {
        if (n_ == 1)
            return "10^" + vtos(trailingZeroCount);
        else if (n_ == -1)
            return "-10^" + vtos(trailingZeroCount);
        else
            return vtos(n_) + "*10^" + vtos(trailingZeroCount);
    } else
        return vtos(n);
}

/* unsigned case. */
template<typename T>
static std::string toHumanReadableString(const T &n, std::true_type) {
    if (n == 0)
        return vtos(n);
    int trailingZeroCount = 0;
    T n_ = n;
    while (n_ % 10 == 0)
        n_ /= 10, trailingZeroCount++;
    if (trailingZeroCount >= 7) {
        if (n_ == 1)
            return "10^" + vtos(trailingZeroCount);
        else
            return vtos(n_) + "*10^" + vtos(trailingZeroCount);
    } else
        return vtos(n);
}

template<typename T>
static std::string toHumanReadableString(const T &n) {
    return toHumanReadableString(n, std::is_unsigned<T>());
}
#else
template<typename T>
static std::string vtos(const T& t)
{
    std::string s;
    static std::stringstream ss;
    ss.str(std::string());
    ss.clear();
    ss << t;
    ss >> s;
    return s;
}

template<typename T>
static std::string toHumanReadableString(const T &n) {
    return vtos(n);
}
#endif

template<typename T>
static std::string toString(const T &t) {
    return vtos(t);
}

#if __cplusplus > 199711L || defined(_MSC_VER)
/* opts */
void prepareOpts(int argc, char* argv[]);
#endif

/*
 * Very simple regex-like pattern.
 * It used for two purposes: validation and generation.
 *
 * For example, pattern("[a-z]{1,5}").next(rnd) will return
 * random string from lowercase latin letters with length
 * from 1 to 5. It is easier to call rnd.next("[a-z]{1,5}")
 * for the same effect.
 *
 * Another samples:
 * "mike|john" will generate (match) "mike" or "john";
 * "-?[1-9][0-9]{0,3}" will generate (match) non-zero integers from -9999 to 9999;
 * "id-([ac]|b{2})" will generate (match) "id-a", "id-bb", "id-c";
 * "[^0-9]*" will match sequences (empty or non-empty) without digits, you can't
 * use it for generations.
 *
 * You can't use pattern for generation if it contains meta-symbol '*'. Also it
 * is not recommended to use it for char-sets with meta-symbol '^' like [^a-z].
 *
 * For matching very simple greedy algorithm is used. For example, pattern
 * "[0-9]?1" will not match "1", because of greedy nature of matching.
 * Alternations (meta-symbols "|") are processed with brute-force algorithm, so
 * do not use many alternations in one expression.
 *
 * If you want to use one expression many times it is better to compile it into
 * a single pattern like "pattern p("[a-z]+")". Later you can use
 * "p.matches(std::string s)" or "p.next(random_t& rd)" to check matching or generate
 * new string by pattern.
 *
 * Simpler way to read token and check it for pattern matching is "inf.readToken("[a-z]+")".
 *
 * All spaces are ignored in regex, unless escaped with \. For example, ouf.readLine("NO SOLUTION")
 * will expect "NOSOLUTION", the correct call should be ouf.readLine("NO\\ SOLUTION") or
 * ouf.readLine(R"(NO\ SOLUTION)") if you prefer raw string literals from C++11.
 */
class random_t;

class pattern {
public:
    /* Create pattern instance by string. */
    pattern(std::string s);

    /* Generate new string by pattern and given random_t. */
    std::string next(random_t &rnd) const;

    /* Checks if given string match the pattern. */
    bool matches(const std::string &s) const;

    /* Returns source string of the pattern. */
    std::string src() const;

private:
    bool matches(const std::string &s, size_t pos) const;

    std::string s;
    std::vector<pattern> children;
    std::vector<char> chars;
    int from;
    int to;
};

/*
 * Use random_t instances to generate random values. It is preferred
 * way to use randoms instead of rand() function or self-written
 * randoms.
 *
 * Testlib defines global variable "rnd" of random_t class.
 * Use registerGen(argc, argv, 1) to setup random_t seed be command
 * line (to use latest random generator version).
 *
 * Random generates uniformly distributed values if another strategy is
 * not specified explicitly.
 */
class random_t {
private:
    unsigned long long seed;
    static const unsigned long long multiplier;
    static const unsigned long long addend;
    static const unsigned long long mask;
    static const int lim;

    long long nextBits(int bits) {
        if (bits <= 48) {
            seed = (seed * multiplier + addend) & mask;
            return (long long) (seed >> (48 - bits));
        } else {
            if (bits > 63)
                __testlib_fail("random_t::nextBits(int bits): n must be less than 64");

            int lowerBitCount = (random_t::version == 0 ? 31 : 32);

            long long left = (nextBits(31) << 32);
            long long right = nextBits(lowerBitCount);

            return left ^ right;
        }
    }

public:
    static int version;

    /* New random_t with fixed seed. */
    random_t()
            : seed(3905348978240129619LL) {
    }

    /* Sets seed by command line. */
    void setSeed(int argc, char *argv[]) {
        random_t p;

        seed = 3905348978240129619LL;
        for (int i = 1; i < argc; i++) {
            std::size_t le = std::strlen(argv[i]);
            for (std::size_t j = 0; j < le; j++)
                seed = seed * multiplier + (unsigned int) (argv[i][j]) + addend;
            seed += multiplier / addend;
        }

        seed = seed & mask;
    }

    /* Sets seed by given value. */
    void setSeed(long long _seed) {
        seed = (unsigned long long) _seed;
        seed = (seed ^ multiplier) & mask;
    }

#ifndef __BORLANDC__

    /* Random string value by given pattern (see pattern documentation). */
    std::string next(const std::string &ptrn) {
        pattern p(ptrn);
        return p.next(*this);
    }

#else
    /* Random string value by given pattern (see pattern documentation). */
    std::string next(std::string ptrn)
    {
        pattern p(ptrn);
        return p.next(*this);
    }
#endif

    /* Random value in range [0, n-1]. */
    int next(int n) {
        if (n <= 0)
            __testlib_fail("random_t::next(int n): n must be positive");

        if ((n & -n) == n)  // n is a power of 2
            return (int) ((n * (long long) nextBits(31)) >> 31);

        const long long limit = INT_MAX / n * n;

        long long bits;
        do {
            bits = nextBits(31);
        } while (bits >= limit);

        return int(bits % n);
    }

    /* Random value in range [0, n-1]. */
    unsigned int next(unsigned int n) {
        if (n >= INT_MAX)
            __testlib_fail("random_t::next(unsigned int n): n must be less INT_MAX");
        return (unsigned int) next(int(n));
    }

    /* Random value in range [0, n-1]. */
    long long next(long long n) {
        if (n <= 0)
            __testlib_fail("random_t::next(long long n): n must be positive");

        const long long limit = __TESTLIB_LONGLONG_MAX / n * n;

        long long bits;
        do {
            bits = nextBits(63);
        } while (bits >= limit);

        return bits % n;
    }

    /* Random value in range [0, n-1]. */
    unsigned long long next(unsigned long long n) {
        if (n >= (unsigned long long) (__TESTLIB_LONGLONG_MAX))
            __testlib_fail("random_t::next(unsigned long long n): n must be less LONGLONG_MAX");
        return (unsigned long long) next((long long) (n));
    }

    /* Random value in range [0, n-1]. */
    long next(long n) {
        return (long) next((long long) (n));
    }

    /* Random value in range [0, n-1]. */
    unsigned long next(unsigned long n) {
        if (n >= (unsigned long) (LONG_MAX))
            __testlib_fail("random_t::next(unsigned long n): n must be less LONG_MAX");
        return (unsigned long) next((unsigned long long) (n));
    }

    /* Returns random value in range [from,to]. */
    int next(int from, int to) {
        return int(next((long long) to - from + 1) + from);
    }

    /* Returns random value in range [from,to]. */
    unsigned int next(unsigned int from, unsigned int to) {
        return (unsigned int) (next((long long) to - from + 1) + from);
    }

    /* Returns random value in range [from,to]. */
    long long next(long long from, long long to) {
        return next(to - from + 1) + from;
    }

    /* Returns random value in range [from,to]. */
    unsigned long long next(unsigned long long from, unsigned long long to) {
        if (from > to)
            __testlib_fail("random_t::next(unsigned long long from, unsigned long long to): from can't not exceed to");
        return next(to - from + 1) + from;
    }

    /* Returns random value in range [from,to]. */
    long next(long from, long to) {
        return next(to - from + 1) + from;
    }

    /* Returns random value in range [from,to]. */
    unsigned long next(unsigned long from, unsigned long to) {
        if (from > to)
            __testlib_fail("random_t::next(unsigned long from, unsigned long to): from can't not exceed to");
        return next(to - from + 1) + from;
    }

    /* Random double value in range [0, 1). */
    double next() {
        long long left = ((long long) (nextBits(26)) << 27);
        long long right = nextBits(27);
        return __testlib_crop((double) (left + right) / (double) (1LL << 53), 0.0, 1.0);
    }

    /* Random double value in range [0, n). */
    double next(double n) {
        if (n <= 0.0)
            __testlib_fail("random_t::next(double): n should be positive");
        return __testlib_crop(n * next(), 0.0, n);
    }

    /* Random double value in range [from, to). */
    double next(double from, double to) {
        if (from >= to)
            __testlib_fail("random_t::next(double from, double to): from should be strictly less than to");
        return next(to - from) + from;
    }

    /* Returns random element from container. */
    template<typename Container>
    typename Container::value_type any(const Container &c) {
        int size = int(c.size());
        if (size <= 0)
            __testlib_fail("random_t::any(const Container& c): c.size() must be positive");
        typename Container::const_iterator it = c.begin();
        std::advance(it, next(size));
        return *it;
    }

    /* Returns random element from iterator range. */
    template<typename Iter>
    typename Iter::value_type any(const Iter &begin, const Iter &end) {
        int size = static_cast<int>(std::distance(begin, end));
        if (size <= 0)
            __testlib_fail("random_t::any(const Iter& begin, const Iter& end): range must have positive length");
        Iter it = begin;
        std::advance(it, next(size));
        return *it;
    }

    /* Random string value by given pattern (see pattern documentation). */
#ifdef __GNUC__
    __attribute__ ((format (printf, 2, 3)))
#endif
    std::string next(const char *format, ...) {
        FMT_TO_RESULT(format, format, ptrn);
        return next(ptrn);
    }

    /*
     * Weighted next. If type == 0 than it is usual "next()".
     *
     * If type = 1, than it returns "max(next(), next())"
     * (the number of "max" functions equals to "type").
     *
     * If type < 0, than "max" function replaces with "min".
     */
    int wnext(int n, int type) {
        if (n <= 0)
            __testlib_fail("random_t::wnext(int n, int type): n must be positive");

        if (abs(type) < random_t::lim) {
            int result = next(n);

            for (int i = 0; i < +type; i++)
                result = __testlib_max(result, next(n));

            for (int i = 0; i < -type; i++)
                result = __testlib_min(result, next(n));

            return result;
        } else {
            double p;

            if (type > 0)
                p = std::pow(next() + 0.0, 1.0 / (type + 1));
            else
                p = 1 - std::pow(next() + 0.0, 1.0 / (-type + 1));

            return __testlib_crop((int) (double(n) * p), 0, n);
        }
    }

    /* See wnext(int, int). It uses the same algorithms. */
    long long wnext(long long n, int type) {
        if (n <= 0)
            __testlib_fail("random_t::wnext(long long n, int type): n must be positive");

        if (abs(type) < random_t::lim) {
            long long result = next(n);

            for (int i = 0; i < +type; i++)
                result = __testlib_max(result, next(n));

            for (int i = 0; i < -type; i++)
                result = __testlib_min(result, next(n));

            return result;
        } else {
            double p;

            if (type > 0)
                p = std::pow(next() + 0.0, 1.0 / (type + 1));
            else
                p = 1 - std::pow(next() + 0.0, 1.0 / (-type + 1));

            return __testlib_crop((long long) (double(n) * p), 0LL, n);
        }
    }

    /* Returns value in [0, n). See wnext(int, int). It uses the same algorithms. */
    double wnext(double n, int type) {
        if (n <= 0)
            __testlib_fail("random_t::wnext(double n, int type): n must be positive");

        if (abs(type) < random_t::lim) {
            double result = next();

            for (int i = 0; i < +type; i++)
                result = __testlib_max(result, next());

            for (int i = 0; i < -type; i++)
                result = __testlib_min(result, next());

            return n * result;
        } else {
            double p;

            if (type > 0)
                p = std::pow(next() + 0.0, 1.0 / (type + 1));
            else
                p = 1 - std::pow(next() + 0.0, 1.0 / (-type + 1));

            return __testlib_crop(n * p, 0.0, n);
        }
    }

    /* Returns value in [0, 1). See wnext(int, int). It uses the same algorithms. */
    double wnext(int type) {
        return wnext(1.0, type);
    }

    /* See wnext(int, int). It uses the same algorithms. */
    unsigned int wnext(unsigned int n, int type) {
        if (n >= INT_MAX)
            __testlib_fail("random_t::wnext(unsigned int n, int type): n must be less INT_MAX");
        return (unsigned int) wnext(int(n), type);
    }

    /* See wnext(int, int). It uses the same algorithms. */
    unsigned long long wnext(unsigned long long n, int type) {
        if (n >= (unsigned long long) (__TESTLIB_LONGLONG_MAX))
            __testlib_fail("random_t::wnext(unsigned long long n, int type): n must be less LONGLONG_MAX");

        return (unsigned long long) wnext((long long) (n), type);
    }

    /* See wnext(int, int). It uses the same algorithms. */
    long wnext(long n, int type) {
        return (long) wnext((long long) (n), type);
    }

    /* See wnext(int, int). It uses the same algorithms. */
    unsigned long wnext(unsigned long n, int type) {
        if (n >= (unsigned long) (LONG_MAX))
            __testlib_fail("random_t::wnext(unsigned long n, int type): n must be less LONG_MAX");

        return (unsigned long) wnext((unsigned long long) (n), type);
    }

    /* Returns weighted random value in range [from, to]. */
    int wnext(int from, int to, int type) {
        if (from > to)
            __testlib_fail("random_t::wnext(int from, int to, int type): from can't not exceed to");
        return wnext(to - from + 1, type) + from;
    }

    /* Returns weighted random value in range [from, to]. */
    int wnext(unsigned int from, unsigned int to, int type) {
        if (from > to)
            __testlib_fail("random_t::wnext(unsigned int from, unsigned int to, int type): from can't not exceed to");
        return int(wnext(to - from + 1, type) + from);
    }

    /* Returns weighted random value in range [from, to]. */
    long long wnext(long long from, long long to, int type) {
        if (from > to)
            __testlib_fail("random_t::wnext(long long from, long long to, int type): from can't not exceed to");
        return wnext(to - from + 1, type) + from;
    }

    /* Returns weighted random value in range [from, to]. */
    unsigned long long wnext(unsigned long long from, unsigned long long to, int type) {
        if (from > to)
            __testlib_fail(
                    "random_t::wnext(unsigned long long from, unsigned long long to, int type): from can't not exceed to");
        return wnext(to - from + 1, type) + from;
    }

    /* Returns weighted random value in range [from, to]. */
    long wnext(long from, long to, int type) {
        if (from > to)
            __testlib_fail("random_t::wnext(long from, long to, int type): from can't not exceed to");
        return wnext(to - from + 1, type) + from;
    }

    /* Returns weighted random value in range [from, to]. */
    unsigned long wnext(unsigned long from, unsigned long to, int type) {
        if (from > to)
            __testlib_fail("random_t::wnext(unsigned long from, unsigned long to, int type): from can't not exceed to");
        return wnext(to - from + 1, type) + from;
    }

    /* Returns weighted random double value in range [from, to). */
    double wnext(double from, double to, int type) {
        if (from >= to)
            __testlib_fail("random_t::wnext(double from, double to, int type): from should be strictly less than to");
        return wnext(to - from, type) + from;
    }

    /* Returns weighted random element from container. */
    template<typename Container>
    typename Container::value_type wany(const Container &c, int type) {
        int size = int(c.size());
        if (size <= 0)
            __testlib_fail("random_t::wany(const Container& c, int type): c.size() must be positive");
        typename Container::const_iterator it = c.begin();
        std::advance(it, wnext(size, type));
        return *it;
    }

    /* Returns weighted random element from iterator range. */
    template<typename Iter>
    typename Iter::value_type wany(const Iter &begin, const Iter &end, int type) {
        int size = static_cast<int>(std::distance(begin, end));
        if (size <= 0)
            __testlib_fail(
                    "random_t::any(const Iter& begin, const Iter& end, int type): range must have positive length");
        Iter it = begin;
        std::advance(it, wnext(size, type));
        return *it;
    }

    /* Returns random permutation of the given size (values are between `first` and `first`+size-1)*/
    template<typename T, typename E>
    std::vector<E> perm(T size, E first) {
        if (size < 0)
            __testlib_fail("random_t::perm(T size, E first = 0): size must non-negative");
        else if (size == 0)
            return std::vector<E>();
        std::vector<E> p(size);
        E current = first;
        for (T i = 0; i < size; i++)
            p[i] = current++;
        if (size > 1)
            for (T i = 1; i < size; i++)
                std::swap(p[i], p[next(i + 1)]);
        return p;
    }

    /* Returns random permutation of the given size (values are between 0 and size-1)*/
    template<typename T>
    std::vector<T> perm(T size) {
        return perm(size, T(0));
    }

    /* Returns `size` unordered (unsorted) distinct numbers between `from` and `to`. */
    template<typename T>
    std::vector<T> distinct(int size, T from, T to) {
        std::vector<T> result;
        if (size == 0)
            return result;

        if (from > to)
            __testlib_fail("random_t::distinct expected from <= to");

        if (size < 0)
            __testlib_fail("random_t::distinct expected size >= 0");

        uint64_t n = to - from + 1;
        if (uint64_t(size) > n)
            __testlib_fail("random_t::distinct expected size <= to - from + 1");

        double expected = 0.0;
        for (int i = 1; i <= size; i++)
            expected += double(n) / double(n - i + 1);

        if (expected < double(n)) {
            std::set<T> vals;
            while (int(vals.size()) < size) {
                T x = T(next(from, to));
                if (vals.insert(x).second)
                    result.push_back(x);
            }
        } else {
            if (n > 1000000000)
                __testlib_fail("random_t::distinct here expected to - from + 1 <= 1000000000");
            std::vector<T> p(perm(int(n), from));
            result.insert(result.end(), p.begin(), p.begin() + size);
        }

        return result;
    }

    /* Returns `size` unordered (unsorted) distinct numbers between `0` and `upper`-1. */
    template<typename T>
    std::vector<T> distinct(int size, T upper) {
        if (size < 0)
            __testlib_fail("random_t::distinct expected size >= 0");
        if (size == 0)
            return std::vector<T>();

        if (upper <= 0)
            __testlib_fail("random_t::distinct expected upper > 0");
        if (size > upper)
            __testlib_fail("random_t::distinct expected size <= upper");

        return distinct(size, T(0), upper - 1);
    }

    /* Returns random (unsorted) partition which is a representation of sum as a sum of integers not less than min_part. */
    template<typename T>
    std::vector<T> partition(int size, T sum, T min_part) {
        if (size < 0)
            __testlib_fail("random_t::partition: size < 0");
        if (size == 0 && sum != 0)
            __testlib_fail("random_t::partition: size == 0 && sum != 0");
        if (min_part * size > sum)
            __testlib_fail("random_t::partition: min_part * size > sum");
        if (size == 0 && sum == 0)
            return std::vector<T>();

        T sum_ = sum;
        sum -= min_part * size;

        std::vector<T> septums(size);
        std::vector<T> d = distinct(size - 1, T(1), T(sum + size - 1));
        for (int i = 0; i + 1 < size; i++)
            septums[i + 1] = d[i];
        sort(septums.begin(), septums.end());

        std::vector<T> result(size);
        for (int i = 0; i + 1 < size; i++)
            result[i] = septums[i + 1] - septums[i] - 1;
        result[size - 1] = sum + size - 1 - septums.back();

        for (std::size_t i = 0; i < result.size(); i++)
            result[i] += min_part;

        T result_sum = 0;
        for (std::size_t i = 0; i < result.size(); i++)
            result_sum += result[i];
        if (result_sum != sum_)
            __testlib_fail("random_t::partition: partition sum is expected to be the given sum");

        if (*std::min_element(result.begin(), result.end()) < min_part)
            __testlib_fail("random_t::partition: partition min is expected to be no less than the given min_part");

        if (int(result.size()) != size || result.size() != (size_t) size)
            __testlib_fail("random_t::partition: partition size is expected to be equal to the given size");

        return result;
    }

    /* Returns random (unsorted) partition which is a representation of sum as a sum of positive integers. */
    template<typename T>
    std::vector<T> partition(int size, T sum) {
        return partition(size, sum, T(1));
    }
};

const int random_t::lim = 25;
const unsigned long long random_t::multiplier = 0x5DEECE66DLL;
const unsigned long long random_t::addend = 0xBLL;
const unsigned long long random_t::mask = (1LL << 48) - 1;
int random_t::version = -1;

/* Pattern implementation */
bool pattern::matches(const std::string &s) const {
    return matches(s, 0);
}

static bool __pattern_isSlash(const std::string &s, size_t pos) {
    return s[pos] == '\\';
}

#ifdef __GNUC__
__attribute__((pure))
#endif
static bool __pattern_isCommandChar(const std::string &s, size_t pos, char value) {
    if (pos >= s.length())
        return false;

    int slashes = 0;

    int before = int(pos) - 1;
    while (before >= 0 && s[before] == '\\')
        before--, slashes++;

    return slashes % 2 == 0 && s[pos] == value;
}

static char __pattern_getChar(const std::string &s, size_t &pos) {
    if (__pattern_isSlash(s, pos))
        pos += 2;
    else
        pos++;

    return s[pos - 1];
}

#ifdef __GNUC__
__attribute__((pure))
#endif
static int __pattern_greedyMatch(const std::string &s, size_t pos, const std::vector<char> chars) {
    int result = 0;

    while (pos < s.length()) {
        char c = s[pos++];
        if (!std::binary_search(chars.begin(), chars.end(), c))
            break;
        else
            result++;
    }

    return result;
}

std::string pattern::src() const {
    return s;
}

bool pattern::matches(const std::string &s, size_t pos) const {
    std::string result;

    if (to > 0) {
        int size = __pattern_greedyMatch(s, pos, chars);
        if (size < from)
            return false;
        if (size > to)
            size = to;
        pos += size;
    }

    if (children.size() > 0) {
        for (size_t child = 0; child < children.size(); child++)
            if (children[child].matches(s, pos))
                return true;
        return false;
    } else
        return pos == s.length();
}

std::string pattern::next(random_t &rnd) const {
    std::string result;
    result.reserve(20);

    if (to == INT_MAX)
        __testlib_fail("pattern::next(random_t& rnd): can't process character '*' for generation");

    if (to > 0) {
        int count = rnd.next(to - from + 1) + from;
        for (int i = 0; i < count; i++)
            result += chars[rnd.next(int(chars.size()))];
    }

    if (children.size() > 0) {
        int child = rnd.next(int(children.size()));
        result += children[child].next(rnd);
    }

    return result;
}

static void __pattern_scanCounts(const std::string &s, size_t &pos, int &from, int &to) {
    if (pos >= s.length()) {
        from = to = 1;
        return;
    }

    if (__pattern_isCommandChar(s, pos, '{')) {
        std::vector<std::string> parts;
        std::string part;

        pos++;

        while (pos < s.length() && !__pattern_isCommandChar(s, pos, '}')) {
            if (__pattern_isCommandChar(s, pos, ','))
                parts.push_back(part), part = "", pos++;
            else
                part += __pattern_getChar(s, pos);
        }

        if (part != "")
            parts.push_back(part);

        if (!__pattern_isCommandChar(s, pos, '}'))
            __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");

        pos++;

        if (parts.size() < 1 || parts.size() > 2)
            __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");

        std::vector<int> numbers;

        for (size_t i = 0; i < parts.size(); i++) {
            if (parts[i].length() == 0)
                __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");
            int number;
            if (std::sscanf(parts[i].c_str(), "%d", &number) != 1)
                __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");
            numbers.push_back(number);
        }

        if (numbers.size() == 1)
            from = to = numbers[0];
        else
            from = numbers[0], to = numbers[1];

        if (from > to)
            __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");
    } else {
        if (__pattern_isCommandChar(s, pos, '?')) {
            from = 0, to = 1, pos++;
            return;
        }

        if (__pattern_isCommandChar(s, pos, '*')) {
            from = 0, to = INT_MAX, pos++;
            return;
        }

        if (__pattern_isCommandChar(s, pos, '+')) {
            from = 1, to = INT_MAX, pos++;
            return;
        }

        from = to = 1;
    }
}

static std::vector<char> __pattern_scanCharSet(const std::string &s, size_t &pos) {
    if (pos >= s.length())
        __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");

    std::vector<char> result;

    if (__pattern_isCommandChar(s, pos, '[')) {
        pos++;
        bool negative = __pattern_isCommandChar(s, pos, '^');
        if (negative)
            pos++;

        char prev = 0;

        while (pos < s.length() && !__pattern_isCommandChar(s, pos, ']')) {
            if (__pattern_isCommandChar(s, pos, '-') && prev != 0) {
                pos++;

                if (pos + 1 == s.length() || __pattern_isCommandChar(s, pos, ']')) {
                    result.push_back(prev);
                    prev = '-';
                    continue;
                }

                char next = __pattern_getChar(s, pos);
                if (prev > next)
                    __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");

                for (char c = prev; c != next; c++)
                    result.push_back(c);
                result.push_back(next);

                prev = 0;
            } else {
                if (prev != 0)
                    result.push_back(prev);
                prev = __pattern_getChar(s, pos);
            }
        }

        if (prev != 0)
            result.push_back(prev);

        if (!__pattern_isCommandChar(s, pos, ']'))
            __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");

        pos++;

        if (negative) {
            std::sort(result.begin(), result.end());
            std::vector<char> actuals;
            for (int code = 0; code < 255; code++) {
                char c = char(code);
                if (!std::binary_search(result.begin(), result.end(), c))
                    actuals.push_back(c);
            }
            result = actuals;
        }

        std::sort(result.begin(), result.end());
    } else
        result.push_back(__pattern_getChar(s, pos));

    return result;
}

pattern::pattern(std::string s) : s(s), from(0), to(0) {
    std::string t;
    for (size_t i = 0; i < s.length(); i++)
        if (!__pattern_isCommandChar(s, i, ' '))
            t += s[i];
    s = t;

    int opened = 0;
    int firstClose = -1;
    std::vector<int> seps;

    for (size_t i = 0; i < s.length(); i++) {
        if (__pattern_isCommandChar(s, i, '(')) {
            opened++;
            continue;
        }

        if (__pattern_isCommandChar(s, i, ')')) {
            opened--;
            if (opened == 0 && firstClose == -1)
                firstClose = int(i);
            continue;
        }

        if (opened < 0)
            __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");

        if (__pattern_isCommandChar(s, i, '|') && opened == 0)
            seps.push_back(int(i));
    }

    if (opened != 0)
        __testlib_fail("pattern: Illegal pattern (or part) \"" + s + "\"");

    if (seps.size() == 0 && firstClose + 1 == (int) s.length()
        && __pattern_isCommandChar(s, 0, '(') && __pattern_isCommandChar(s, s.length() - 1, ')')) {
        children.push_back(pattern(s.substr(1, s.length() - 2)));
    } else {
        if (seps.size() > 0) {
            seps.push_back(int(s.length()));
            int last = 0;

            for (size_t i = 0; i < seps.size(); i++) {
                children.push_back(pattern(s.substr(last, seps[i] - last)));
                last = seps[i] + 1;
            }
        } else {
            size_t pos = 0;
            chars = __pattern_scanCharSet(s, pos);
            __pattern_scanCounts(s, pos, from, to);
            if (pos < s.length())
                children.push_back(pattern(s.substr(pos)));
        }
    }
}

/* End of pattern implementation */

template<typename C>
inline bool isEof(C c) {
    return c == EOFC;
}

template<typename C>
inline bool isEoln(C c) {
    return (c == LF || c == CR);
}

template<typename C>
inline bool isBlanks(C c) {
    return (c == LF || c == CR || c == SPACE || c == TAB);
}

inline std::string trim(const std::string &s) {
    if (s.empty())
        return s;

    int left = 0;
    while (left < int(s.length()) && isBlanks(s[left]))
        left++;
    if (left >= int(s.length()))
        return "";

    int right = int(s.length()) - 1;
    while (right >= 0 && isBlanks(s[right]))
        right--;
    if (right < 0)
        return "";

    return s.substr(left, right - left + 1);
}

enum TMode {
    _input, _output, _answer
};

/* Outcomes 6-15 are reserved for future use. */
enum TResult {
    _ok = 0,
    _wa = 1,
    _pe = 2,
    _fail = 3,
    _dirt = 4,
    _points = 5,
    _unexpected_eof = 8,
    _partially = 16
};

enum TTestlibMode {
    _unknown, _checker, _validator, _generator, _interactor, _scorer
};

#define _pc(exitCode) (TResult(_partially + (exitCode)))

/* Outcomes 6-15 are reserved for future use. */
const std::string outcomes[] = {
        "accepted",
        "wrong-answer",
        "presentation-error",
        "fail",
        "fail",
#ifndef PCMS2
        "points",
#else
        "relative-scoring",
#endif
        "reserved",
        "reserved",
        "unexpected-eof",
        "reserved",
        "reserved",
        "reserved",
        "reserved",
        "reserved",
        "reserved",
        "reserved",
        "partially-correct"
};

class InputStreamReader {
public:
    virtual void setTestCase(int testCase) = 0;

    virtual std::vector<int> getReadChars() = 0;
    
    virtual int curChar() = 0;

    virtual int nextChar() = 0;

    virtual void skipChar() = 0;

    virtual void unreadChar(int c) = 0;

    virtual std::string getName() = 0;

    virtual bool eof() = 0;

    virtual void close() = 0;

    virtual int getLine() = 0;

    virtual ~InputStreamReader() = 0;
};

InputStreamReader::~InputStreamReader() {
    // No operations.
}

class StringInputStreamReader : public InputStreamReader {
private:
    std::string s;
    size_t pos;

public:
    StringInputStreamReader(const std::string &content) : s(content), pos(0) {
        // No operations.
    }

    void setTestCase(int) {
        __testlib_fail("setTestCase not implemented in StringInputStreamReader");
    }

    std::vector<int> getReadChars() {
        __testlib_fail("getReadChars not implemented in StringInputStreamReader");
    }
    
    int curChar() {
        if (pos >= s.length())
            return EOFC;
        else
            return s[pos];
    }

    int nextChar() {
        if (pos >= s.length()) {
            pos++;
            return EOFC;
        } else
            return s[pos++];
    }

    void skipChar() {
        pos++;
    }

    void unreadChar(int c) {
        if (pos == 0)
            __testlib_fail("StringInputStreamReader::unreadChar(int): pos == 0.");
        pos--;
        if (pos < s.length())
            s[pos] = char(c);
    }

    std::string getName() {
        return __testlib_part(s);
    }

    int getLine() {
        return -1;
    }

    bool eof() {
        return pos >= s.length();
    }

    void close() {
        // No operations.
    }
};

class FileInputStreamReader : public InputStreamReader {
private:
    std::FILE *file;
    std::string name;
    int line;
    std::vector<int> undoChars;
    std::vector<int> readChars;
    std::vector<int> undoReadChars;

    inline int postprocessGetc(int getcResult) {
        if (getcResult != EOF)
            return getcResult;
        else
            return EOFC;
    }

    int getc(FILE *file) {
        int c;
        int rc;

        if (undoChars.empty()) {
            c = rc = ::getc(file);
        } else {
            c = undoChars.back();
            undoChars.pop_back();
            rc = undoReadChars.back();
            undoReadChars.pop_back();
        }

        if (c == LF)
            line++;

        readChars.push_back(rc);
        return c;
    }

    int ungetc(int c/*, FILE* file*/) {
        if (!readChars.empty()) {
            undoReadChars.push_back(readChars.back());
            readChars.pop_back();
        }
        if (c == LF)
            line--;
        undoChars.push_back(c);
        return c;
    }

public:
    FileInputStreamReader(std::FILE *file, const std::string &name) : file(file), name(name), line(1) {
        // No operations.
    }

    void setTestCase(int testCase) {
        if (testCase < 0 || testCase > __TESTLIB_MAX_TEST_CASE)
            __testlib_fail(format("testCase expected fit in [1,%d], but %d doesn't", __TESTLIB_MAX_TEST_CASE, testCase));
        readChars.push_back(testCase + 256);
    }

    std::vector<int> getReadChars() {
        return readChars;
    }

    int curChar() {
        if (feof(file))
            return EOFC;
        else {
            int c = getc(file);
            ungetc(c/*, file*/);
            return postprocessGetc(c);
        }
    }

    int nextChar() {
        if (feof(file))
            return EOFC;
        else
            return postprocessGetc(getc(file));
    }

    void skipChar() {
        getc(file);
    }

    void unreadChar(int c) {
        ungetc(c/*, file*/);
    }

    std::string getName() {
        return name;
    }

    int getLine() {
        return line;
    }

    bool eof() {
        if (NULL == file || feof(file))
            return true;
        else {
            int c = nextChar();
            if (c == EOFC || (c == EOF && feof(file)))
                return true;
            unreadChar(c);
            return false;
        }
    }

    void close() {
        if (NULL != file) {
            fclose(file);
            file = NULL;
        }
    }
};

class BufferedFileInputStreamReader : public InputStreamReader {
private:
    static const size_t BUFFER_SIZE;
    static const size_t MAX_UNREAD_COUNT;

    std::FILE *file;
    std::string name;
    int line;

    char *buffer;
    bool *isEof;
    int bufferPos;
    size_t bufferSize;

    bool refill() {
        if (NULL == file)
            __testlib_fail("BufferedFileInputStreamReader: file == NULL (" + getName() + ")");

        if (bufferPos >= int(bufferSize)) {
            size_t readSize = fread(
                    buffer + MAX_UNREAD_COUNT,
                    1,
                    BUFFER_SIZE - MAX_UNREAD_COUNT,
                    file
            );

            if (readSize < BUFFER_SIZE - MAX_UNREAD_COUNT
                && ferror(file))
                __testlib_fail("BufferedFileInputStreamReader: unable to read (" + getName() + ")");

            bufferSize = MAX_UNREAD_COUNT + readSize;
            bufferPos = int(MAX_UNREAD_COUNT);
            std::memset(isEof + MAX_UNREAD_COUNT, 0, sizeof(isEof[0]) * readSize);

            return readSize > 0;
        } else
            return true;
    }

    char increment() {
        char c;
        if ((c = buffer[bufferPos++]) == LF)
            line++;
        return c;
    }

public:
    BufferedFileInputStreamReader(std::FILE *file, const std::string &name) : file(file), name(name), line(1) {
        buffer = new char[BUFFER_SIZE];
        isEof = new bool[BUFFER_SIZE];
        bufferSize = MAX_UNREAD_COUNT;
        bufferPos = int(MAX_UNREAD_COUNT);
    }

    ~BufferedFileInputStreamReader() {
        if (NULL != buffer) {
            delete[] buffer;
            buffer = NULL;
        }
        if (NULL != isEof) {
            delete[] isEof;
            isEof = NULL;
        }
    }

    void setTestCase(int) {
        __testlib_fail("setTestCase not implemented in BufferedFileInputStreamReader");
    }

    std::vector<int> getReadChars() {
        __testlib_fail("getReadChars not implemented in BufferedFileInputStreamReader");
    }
    
    int curChar() {
        if (!refill())
            return EOFC;

        return isEof[bufferPos] ? EOFC : buffer[bufferPos];
    }

    int nextChar() {
        if (!refill())
            return EOFC;

        return isEof[bufferPos] ? EOFC : increment();
    }

    void skipChar() {
        increment();
    }

    void unreadChar(int c) {
        bufferPos--;
        if (bufferPos < 0)
            __testlib_fail("BufferedFileInputStreamReader::unreadChar(int): bufferPos < 0");
        isEof[bufferPos] = (c == EOFC);
        buffer[bufferPos] = char(c);
        if (c == LF)
            line--;
    }

    std::string getName() {
        return name;
    }

    int getLine() {
        return line;
    }

    bool eof() {
        return !refill() || EOFC == curChar();
    }

    void close() {
        if (NULL != file) {
            fclose(file);
            file = NULL;
        }
    }
};

const size_t BufferedFileInputStreamReader::BUFFER_SIZE = 2000000;
const size_t BufferedFileInputStreamReader::MAX_UNREAD_COUNT = BufferedFileInputStreamReader::BUFFER_SIZE / 2;

/*
 * Streams to be used for reading data in checkers or validators.
 * Each read*() method moves pointer to the next character after the
 * read value.
 */
struct InStream {
    /* Do not use them. */
    InStream();

    ~InStream();

    /* Wrap std::string with InStream. */
    InStream(const InStream &baseStream, std::string content);

    InputStreamReader *reader;
    int lastLine;

    std::string name;
    TMode mode;
    bool opened;
    bool stdfile;
    bool strict;

    int wordReserveSize;
    std::string _tmpReadToken;

    int readManyIteration;
    size_t maxFileSize;
    size_t maxTokenLength;
    size_t maxMessageLength;

    void init(std::string fileName, TMode mode);

    void init(std::FILE *f, TMode mode);

    void setTestCase(int testCase);
    std::vector<int> getReadChars();

    /* Moves stream pointer to the first non-white-space character or EOF. */
    void skipBlanks();

    /* Returns current character in the stream. Doesn't remove it from stream. */
    char curChar();

    /* Moves stream pointer one character forward. */
    void skipChar();

    /* Returns current character and moves pointer one character forward. */
    char nextChar();

    /* Returns current character and moves pointer one character forward. */
    char readChar();

    /* As "readChar()" but ensures that the result is equal to given parameter. */
    char readChar(char c);

    /* As "readChar()" but ensures that the result is equal to the space (code=32). */
    char readSpace();

    /* Puts back the character into the stream. */
    void unreadChar(char c);

    /* Reopens stream, you should not use it. */
    void reset(std::FILE *file = NULL);

    /* Checks that current position is EOF. If not it doesn't move stream pointer. */
    bool eof();

    /* Moves pointer to the first non-white-space character and calls "eof()". */
    bool seekEof();

    /*
     * Checks that current position contains EOLN.
     * If not it doesn't move stream pointer.
     * In strict mode expects "#13#10" for windows or "#10" for other platforms.
     */
    bool eoln();

    /* Moves pointer to the first non-space and non-tab character and calls "eoln()". */
    bool seekEoln();

    /* Moves stream pointer to the first character of the next line (if exists). */
    void nextLine();

    /*
     * Reads new token. Ignores white-spaces into the non-strict mode
     * (strict mode is used in validators usually).
     */
    std::string readWord();

    /* The same as "readWord()", it is preferred to use "readToken()". */
    std::string readToken();

    /* The same as "readWord()", but ensures that token matches to given pattern. */
    std::string readWord(const std::string &ptrn, const std::string &variableName = "");

    std::string readWord(const pattern &p, const std::string &variableName = "");

    std::vector<std::string>
    readWords(int size, const std::string &ptrn, const std::string &variablesName = "", int indexBase = 1);

    std::vector<std::string>
    readWords(int size, const pattern &p, const std::string &variablesName = "", int indexBase = 1);

    std::vector<std::string> readWords(int size, int indexBase = 1);

    /* The same as "readToken()", but ensures that token matches to given pattern. */
    std::string readToken(const std::string &ptrn, const std::string &variableName = "");

    std::string readToken(const pattern &p, const std::string &variableName = "");

    std::vector<std::string>
    readTokens(int size, const std::string &ptrn, const std::string &variablesName = "", int indexBase = 1);

    std::vector<std::string>
    readTokens(int size, const pattern &p, const std::string &variablesName = "", int indexBase = 1);

    std::vector<std::string> readTokens(int size, int indexBase = 1);

    void readWordTo(std::string &result);

    void readWordTo(std::string &result, const pattern &p, const std::string &variableName = "");

    void readWordTo(std::string &result, const std::string &ptrn, const std::string &variableName = "");

    void readTokenTo(std::string &result);

    void readTokenTo(std::string &result, const pattern &p, const std::string &variableName = "");

    void readTokenTo(std::string &result, const std::string &ptrn, const std::string &variableName = "");

    /*
     * Reads new long long value. Ignores white-spaces into the non-strict mode
     * (strict mode is used in validators usually).
     */
    long long readLong();

    unsigned long long readUnsignedLong();

    /*
     * Reads new int. Ignores white-spaces into the non-strict mode
     * (strict mode is used in validators usually).
     */
    int readInteger();

    /*
     * Reads new int. Ignores white-spaces into the non-strict mode
     * (strict mode is used in validators usually).
     */
    int readInt();

    /* As "readLong()" but ensures that value in the range [minv,maxv]. */
    long long readLong(long long minv, long long maxv, const std::string &variableName = "");

    /* Reads space-separated sequence of long longs. */
    std::vector<long long>
    readLongs(int size, long long minv, long long maxv, const std::string &variablesName = "", int indexBase = 1);

    /* Reads space-separated sequence of long longs. */
    std::vector<long long> readLongs(int size, int indexBase = 1);

    unsigned long long
    readUnsignedLong(unsigned long long minv, unsigned long long maxv, const std::string &variableName = "");

    std::vector<unsigned long long>
    readUnsignedLongs(int size, unsigned long long minv, unsigned long long maxv, const std::string &variablesName = "",
                      int indexBase = 1);

    std::vector<unsigned long long> readUnsignedLongs(int size, int indexBase = 1);

    unsigned long long readLong(unsigned long long minv, unsigned long long maxv, const std::string &variableName = "");

    std::vector<unsigned long long>
    readLongs(int size, unsigned long long minv, unsigned long long maxv, const std::string &variablesName = "",
              int indexBase = 1);

    /* As "readInteger()" but ensures that value in the range [minv,maxv]. */
    int readInteger(int minv, int maxv, const std::string &variableName = "");

    /* As "readInt()" but ensures that value in the range [minv,maxv]. */
    int readInt(int minv, int maxv, const std::string &variableName = "");

    /* Reads space-separated sequence of integers. */
    std::vector<int>
    readIntegers(int size, int minv, int maxv, const std::string &variablesName = "", int indexBase = 1);

    /* Reads space-separated sequence of integers. */
    std::vector<int> readIntegers(int size, int indexBase = 1);

    /* Reads space-separated sequence of integers. */
    std::vector<int> readInts(int size, int minv, int maxv, const std::string &variablesName = "", int indexBase = 1);

    /* Reads space-separated sequence of integers. */
    std::vector<int> readInts(int size, int indexBase = 1);

    /*
     * Reads new double. Ignores white-spaces into the non-strict mode
     * (strict mode is used in validators usually).
     */
    double readReal();

    /*
     * Reads new double. Ignores white-spaces into the non-strict mode
     * (strict mode is used in validators usually).
     */
    double readDouble();

    /* As "readReal()" but ensures that value in the range [minv,maxv]. */
    double readReal(double minv, double maxv, const std::string &variableName = "");

    std::vector<double>
    readReals(int size, double minv, double maxv, const std::string &variablesName = "", int indexBase = 1);

    std::vector<double> readReals(int size, int indexBase = 1);

    /* As "readDouble()" but ensures that value in the range [minv,maxv]. */
    double readDouble(double minv, double maxv, const std::string &variableName = "");

    std::vector<double>
    readDoubles(int size, double minv, double maxv, const std::string &variablesName = "", int indexBase = 1);

    std::vector<double> readDoubles(int size, int indexBase = 1);

    /*
     * As "readReal()" but ensures that value in the range [minv,maxv] and
     * number of digit after the decimal point is in range [minAfterPointDigitCount,maxAfterPointDigitCount]
     * and number is in the form "[-]digit(s)[.digit(s)]".
     */
    double readStrictReal(double minv, double maxv,
                          int minAfterPointDigitCount, int maxAfterPointDigitCount,
                          const std::string &variableName = "");

    std::vector<double> readStrictReals(int size, double minv, double maxv,
                                        int minAfterPointDigitCount, int maxAfterPointDigitCount,
                                        const std::string &variablesName = "", int indexBase = 1);

    /*
     * As "readDouble()" but ensures that value in the range [minv,maxv] and
     * number of digit after the decimal point is in range [minAfterPointDigitCount,maxAfterPointDigitCount]
     * and number is in the form "[-]digit(s)[.digit(s)]".
     */
    double readStrictDouble(double minv, double maxv,
                            int minAfterPointDigitCount, int maxAfterPointDigitCount,
                            const std::string &variableName = "");

    std::vector<double> readStrictDoubles(int size, double minv, double maxv,
                                          int minAfterPointDigitCount, int maxAfterPointDigitCount,
                                          const std::string &variablesName = "", int indexBase = 1);

    /* As readLine(). */
    std::string readString();

    /* Read many lines. */
    std::vector<std::string> readStrings(int size, int indexBase = 1);

    /* See readLine(). */
    void readStringTo(std::string &result);

    /* The same as "readLine()/readString()", but ensures that line matches to the given pattern. */
    std::string readString(const pattern &p, const std::string &variableName = "");

    /* The same as "readLine()/readString()", but ensures that line matches to the given pattern. */
    std::string readString(const std::string &ptrn, const std::string &variableName = "");

    /* Read many lines. */
    std::vector<std::string>
    readStrings(int size, const pattern &p, const std::string &variableName = "", int indexBase = 1);

    /* Read many lines. */
    std::vector<std::string>
    readStrings(int size, const std::string &ptrn, const std::string &variableName = "", int indexBase = 1);

    /* The same as "readLine()/readString()", but ensures that line matches to the given pattern. */
    void readStringTo(std::string &result, const pattern &p, const std::string &variableName = "");

    /* The same as "readLine()/readString()", but ensures that line matches to the given pattern. */
    void readStringTo(std::string &result, const std::string &ptrn, const std::string &variableName = "");

    /*
     * Reads line from the current position to EOLN or EOF. Moves stream pointer to
     * the first character of the new line (if possible).
     */
    std::string readLine();

    /* Read many lines. */
    std::vector<std::string> readLines(int size, int indexBase = 1);

    /* See readLine(). */
    void readLineTo(std::string &result);

    /* The same as "readLine()", but ensures that line matches to the given pattern. */
    std::string readLine(const pattern &p, const std::string &variableName = "");

    /* The same as "readLine()", but ensures that line matches to the given pattern. */
    std::string readLine(const std::string &ptrn, const std::string &variableName = "");

    /* Read many lines. */
    std::vector<std::string>
    readLines(int size, const pattern &p, const std::string &variableName = "", int indexBase = 1);

    /* Read many lines. */
    std::vector<std::string>
    readLines(int size, const std::string &ptrn, const std::string &variableName = "", int indexBase = 1);

    /* The same as "readLine()", but ensures that line matches to the given pattern. */
    void readLineTo(std::string &result, const pattern &p, const std::string &variableName = "");

    /* The same as "readLine()", but ensures that line matches to the given pattern. */
    void readLineTo(std::string &result, const std::string &ptrn, const std::string &variableName = "");

    /* Reads EOLN or fails. Use it in validators. Calls "eoln()" method internally. */
    void readEoln();

    /* Reads EOF or fails. Use it in validators. Calls "eof()" method internally. */
    void readEof();

    /*
     * Quit-functions aborts program with <result> and <message>:
     * input/answer streams replace any result to FAIL.
     */
    NORETURN void quit(TResult result, const char *msg);
    /*
     * Quit-functions aborts program with <result> and <message>:
     * input/answer streams replace any result to FAIL.
     */
    NORETURN void quitf(TResult result, const char *msg, ...);

    /*
     * Quit-functions aborts program with <result> and <message>:
     * input/answer streams replace any result to FAIL.
     */
    void quitif(bool condition, TResult result, const char *msg, ...);
    /*
     * Quit-functions aborts program with <result> and <message>:
     * input/answer streams replace any result to FAIL.
     */
    NORETURN void quits(TResult result, std::string msg);

    /*
     * Checks condition and aborts a program if condition is false.
     * Returns _wa for ouf and _fail on any other streams.
     */
#ifdef __GNUC__
    __attribute__ ((format (printf, 3, 4)))
#endif
    void ensuref(bool cond, const char *format, ...);

    void __testlib_ensure(bool cond, std::string message);

    void close();

    const static int NO_INDEX = INT_MAX;
    const static char OPEN_BRACKET = char(11);
    const static char CLOSE_BRACKET = char(17);

    const static WORD LightGray = 0x07;
    const static WORD LightRed = 0x0c;
    const static WORD LightCyan = 0x0b;
    const static WORD LightGreen = 0x0a;
    const static WORD LightYellow = 0x0e;

    static void textColor(WORD color);

    static void quitscr(WORD color, const char *msg);

    static void quitscrS(WORD color, std::string msg);

    void xmlSafeWrite(std::FILE *file, const char *msg);

    /* Skips UTF-8 Byte Order Mark. */
    void skipBom();

private:
    InStream(const InStream &);

    InStream &operator=(const InStream &);
};

InStream inf;
InStream ouf;
InStream ans;
bool appesMode;
std::string appesModeEncoding = "windows-1251";
std::string resultName;
std::string checkerName = "untitled checker";
random_t rnd;
TTestlibMode testlibMode = _unknown;
double __testlib_points = std::numeric_limits<float>::infinity();

struct ValidatorBoundsHit {
    static const double EPS;
    bool minHit;
    bool maxHit;

    ValidatorBoundsHit(bool minHit = false, bool maxHit = false) : minHit(minHit), maxHit(maxHit) {
    };

    ValidatorBoundsHit merge(const ValidatorBoundsHit &validatorBoundsHit, bool ignoreMinBound, bool ignoreMaxBound) {
        return ValidatorBoundsHit(
                __testlib_max(minHit, validatorBoundsHit.minHit) || ignoreMinBound,
                __testlib_max(maxHit, validatorBoundsHit.maxHit) || ignoreMaxBound
        );
    }
};

const double ValidatorBoundsHit::EPS = 1E-12;

class Validator {
private:
    const static std::string TEST_MARKUP_HEADER;
    const static std::string TEST_CASE_OPEN_TAG;
    const static std::string TEST_CASE_CLOSE_TAG;

    bool _initialized;
    std::string _testset;
    std::string _group;

    std::string _testOverviewLogFileName;
    std::string _testMarkupFileName;
    int _testCase = -1;
    std::string _testCaseFileName;

    std::map<std::string, ValidatorBoundsHit> _boundsHitByVariableName;
    std::set<std::string> _features;
    std::set<std::string> _hitFeatures;

    bool isVariableNameBoundsAnalyzable(const std::string &variableName) {
        for (size_t i = 0; i < variableName.length(); i++)
            if ((variableName[i] >= '0' && variableName[i] <= '9') || variableName[i] < ' ')
                return false;
        return true;
    }

    bool isFeatureNameAnalyzable(const std::string &featureName) {
        for (size_t i = 0; i < featureName.length(); i++)
            if (featureName[i] < ' ')
                return false;
        return true;
    }

public:
    Validator() : _initialized(false), _testset("tests"), _group() {
    }

    void initialize() {
        _initialized = true;
    }

    std::string testset() const {
        if (!_initialized)
            __testlib_fail("Validator should be initialized with registerValidation(argc, argv) instead of registerValidation() to support validator.testset()");
        return _testset;
    }

    std::string group() const {
        if (!_initialized)
            __testlib_fail("Validator should be initialized with registerValidation(argc, argv) instead of registerValidation() to support validator.group()");
        return _group;
    }

    std::string testOverviewLogFileName() const {
        return _testOverviewLogFileName;
    }

    std::string testMarkupFileName() const {
        return _testMarkupFileName;
    }

    int testCase() const {
        return _testCase;
    }

    std::string testCaseFileName() const {
        return _testCaseFileName;
    }

    void setTestset(const char *const testset) {
        _testset = testset;
    }

    void setGroup(const char *const group) {
        _group = group;
    }

    void setTestOverviewLogFileName(const char *const testOverviewLogFileName) {
        _testOverviewLogFileName = testOverviewLogFileName;
    }

    void setTestMarkupFileName(const char *const testMarkupFileName) {
        _testMarkupFileName = testMarkupFileName;
    }

    void setTestCase(int testCase) {
        _testCase = testCase;
    }

    void setTestCaseFileName(const char *const testCaseFileName) {
        _testCaseFileName = testCaseFileName;
    }

    std::string prepVariableName(const std::string &variableName) {
        if (variableName.length() >= 2 && variableName != "~~") {
            if (variableName[0] == '~' && variableName.back() != '~')
                return variableName.substr(1);
            if (variableName[0] != '~' && variableName.back() == '~')
                return variableName.substr(0, variableName.length() - 1);
            if (variableName[0] == '~' && variableName.back() == '~')
                return variableName.substr(1, variableName.length() - 2);
        }
        return variableName;
    }

    bool ignoreMinBound(const std::string &variableName) {
        return variableName.length() >= 2 && variableName != "~~" && variableName[0] == '~';
    }

    bool ignoreMaxBound(const std::string &variableName) {
        return variableName.length() >= 2 && variableName != "~~" && variableName.back() == '~';
    }

    void addBoundsHit(const std::string &variableName, ValidatorBoundsHit boundsHit) {
        if (isVariableNameBoundsAnalyzable(variableName)) {
            std::string preparedVariableName = prepVariableName(variableName);
            _boundsHitByVariableName[preparedVariableName] = boundsHit.merge(_boundsHitByVariableName[preparedVariableName],
                ignoreMinBound(variableName), ignoreMaxBound(variableName));
        }
    }

    std::string getBoundsHitLog() {
        std::string result;
        for (std::map<std::string, ValidatorBoundsHit>::iterator i = _boundsHitByVariableName.begin();
             i != _boundsHitByVariableName.end();
             i++) {
            result += "\"" + i->first + "\":";
            if (i->second.minHit)
                result += " min-value-hit";
            if (i->second.maxHit)
                result += " max-value-hit";
            result += "\n";
        }
        return result;
    }

    std::string getFeaturesLog() {
        std::string result;
        for (std::set<std::string>::iterator i = _features.begin();
             i != _features.end();
             i++) {
            result += "feature \"" + *i + "\":";
            if (_hitFeatures.count(*i))
                result += " hit";
            result += "\n";
        }
        return result;
    }

    void writeTestOverviewLog() {
        if (!_testOverviewLogFileName.empty()) {
            std::string fileName(_testOverviewLogFileName);
            _testOverviewLogFileName = "";

            FILE* f;
            bool standard_file = false;
            if (fileName == "stdout")
                f = stdout, standard_file = true;
            else if (fileName == "stderr")
                f = stderr, standard_file = true;
            else {
                f = fopen(fileName.c_str(), "wb");
                if (NULL == f)
                    __testlib_fail("Validator::writeTestOverviewLog: can't write test overview log to (" + fileName + ")");
            }
            fprintf(f, "%s%s", getBoundsHitLog().c_str(), getFeaturesLog().c_str());
            std::fflush(f);
            if (!standard_file)
                if (std::fclose(f))
                    __testlib_fail("Validator::writeTestOverviewLog: can't close test overview log file (" + fileName + ")");
        }
    }

    void writeTestMarkup() {
        if (!_testMarkupFileName.empty()) {
            std::vector<int> readChars = inf.getReadChars();
            if (!readChars.empty()) {
                std::string markup(TEST_MARKUP_HEADER);
                for (size_t i = 0; i < readChars.size(); i++) {
                    int c = readChars[i];
                    if (i + 1 == readChars.size() && c == -1)
                        continue;
                    if (c <= 256) {
                        char cc = char(c);
                        if (cc == '\\' || cc == '!')
                            markup += '\\';
                        markup += cc;
                    } else {
                        markup += TEST_CASE_OPEN_TAG;
                        markup += toString(c - 256);
                        markup += TEST_CASE_CLOSE_TAG;
                    }
                }
                FILE* f;
                bool standard_file = false;
                if (_testMarkupFileName == "stdout")
                    f = stdout, standard_file = true;
                else if (_testMarkupFileName == "stderr")
                    f = stderr, standard_file = true;
                else {
                    f = fopen(_testMarkupFileName.c_str(), "wb");
                    if (NULL == f)
                        __testlib_fail("Validator::writeTestMarkup: can't write test markup to (" + _testMarkupFileName + ")");
                }
                std::fprintf(f, "%s", markup.c_str());
                std::fflush(f);
                if (!standard_file)
                    if (std::fclose(f))
                        __testlib_fail("Validator::writeTestMarkup: can't close test markup file (" + _testCaseFileName + ")");
            }
        }
    }

    void writeTestCase() {
        if (_testCase > 0) {
            std::vector<int> readChars = inf.getReadChars();
            if (!readChars.empty()) {
                std::string content, testCaseContent;
                bool matchedTestCase = false;
                for (size_t i = 0; i < readChars.size(); i++) {
                    int c = readChars[i];
                    if (i + 1 == readChars.size() && c == -1)
                        continue;
                    if (c <= 256)
                        content += char(c);
                    else {
                        if (matchedTestCase) {
                            testCaseContent = content;
                            matchedTestCase = false;
                        }
                        content = "";
                        int testCase = c - 256;
                        if (testCase == _testCase)
                            matchedTestCase = true;
                    }
                }
                if (matchedTestCase)
                    testCaseContent = content;

                if (!testCaseContent.empty()) {
                    FILE* f;
                    bool standard_file = false;
                    if (_testCaseFileName.empty() || _testCaseFileName == "stdout")
                        f = stdout, standard_file = true;
                    else if (_testCaseFileName == "stderr")
                        f = stderr, standard_file = true;
                    else {
                        f = fopen(_testCaseFileName.c_str(), "wb");
                        if (NULL == f)
                            __testlib_fail("Validator::writeTestCase: can't write test case to (" + _testCaseFileName + ")");
                    }
                    std::fprintf(f, "%s", testCaseContent.c_str());
                    std::fflush(f);
                    if (!standard_file)
                        if (std::fclose(f))
                            __testlib_fail("Validator::writeTestCase: can't close test case file (" + _testCaseFileName + ")");
                }
            }
        }
    }

    void addFeature(const std::string &feature) {
        if (_features.count(feature))
            __testlib_fail("Feature " + feature + " registered twice.");
        if (!isFeatureNameAnalyzable(feature))
            __testlib_fail("Feature name '" + feature + "' contains restricted characters.");

        _features.insert(feature);
    }

    void feature(const std::string &feature) {
        if (!isFeatureNameAnalyzable(feature))
            __testlib_fail("Feature name '" + feature + "' contains restricted characters.");

        if (!_features.count(feature))
            __testlib_fail("Feature " + feature + " didn't registered via addFeature(feature).");

        _hitFeatures.insert(feature);
    }
} validator;

const std::string Validator::TEST_MARKUP_HEADER = "MU\xF3\x01";
const std::string Validator::TEST_CASE_OPEN_TAG = "!c";
const std::string Validator::TEST_CASE_CLOSE_TAG = ";";

struct TestlibFinalizeGuard {
    static bool alive;
    static bool registered;

    int quitCount, readEofCount;

    TestlibFinalizeGuard() : quitCount(0), readEofCount(0) {
        // No operations.
    }

    ~TestlibFinalizeGuard() {
        bool _alive = alive;
        alive = false;

        if (_alive) {
            if (testlibMode == _checker && quitCount == 0)
                __testlib_fail("Checker must end with quit or quitf call.");

            if (testlibMode == _validator && readEofCount == 0 && quitCount == 0)
                __testlib_fail("Validator must end with readEof call.");

            /* opts */
            autoEnsureNoUnusedOpts();

            if (!registered)
                __testlib_fail("Call register-function in the first line of the main (registerTestlibCmd or other similar)");
        }

        if (__testlib_exitCode == 0) {
            validator.writeTestOverviewLog();
            validator.writeTestMarkup();
            validator.writeTestCase();
        }
    }

private:
    /* opts */
    void autoEnsureNoUnusedOpts();
};

bool TestlibFinalizeGuard::alive = true;
bool TestlibFinalizeGuard::registered = false;
extern TestlibFinalizeGuard testlibFinalizeGuard;

/*
 * Call it to disable checks on finalization.
 */
void disableFinalizeGuard() {
    TestlibFinalizeGuard::alive = false;
}

/* Interactor streams.
 */
std::fstream tout;

/* implementation
 */

InStream::InStream() {
    reader = NULL;
    lastLine = -1;
    opened = false;
    name = "";
    mode = _input;
    strict = false;
    stdfile = false;
    wordReserveSize = 4;
    readManyIteration = NO_INDEX;
    maxFileSize = 128 * 1024 * 1024; // 128MB.
    maxTokenLength = 32 * 1024 * 1024; // 32MB.
    maxMessageLength = 32000;
}

InStream::InStream(const InStream &baseStream, std::string content) {
    reader = new StringInputStreamReader(content);
    lastLine = -1;
    opened = true;
    strict = baseStream.strict;
    stdfile = false;
    mode = baseStream.mode;
    name = "based on " + baseStream.name;
    readManyIteration = NO_INDEX;
    maxFileSize = 128 * 1024 * 1024; // 128MB.
    maxTokenLength = 32 * 1024 * 1024; // 32MB.
    maxMessageLength = 32000;
}

InStream::~InStream() {
    if (NULL != reader) {
        reader->close();
        delete reader;
        reader = NULL;
    }
}

void InStream::setTestCase(int testCase) {
    if (testlibMode != _validator || mode != _input || !stdfile || this != &inf)
        __testlib_fail("InStream::setTestCase can be used only for inf in validator-mode."
            " Actually, prefer setTestCase function instead of InStream member");
    reader->setTestCase(testCase);
}

std::vector<int> InStream::getReadChars() {
    if (testlibMode != _validator || mode != _input || !stdfile || this != &inf)
        __testlib_fail("InStream::getReadChars can be used only for inf in validator-mode.");
    return reader == NULL ? std::vector<int>() : reader->getReadChars();
}

void setTestCase(int testCase) {
    static bool first_run = true;
    static bool zero_based = false;

    if (first_run && testCase == 0)
        zero_based = true;

    if (zero_based)
        testCase++;

    __testlib_hasTestCase = true;
    __testlib_testCase = testCase;

    if (testlibMode == _validator)
        inf.setTestCase(testCase);

    first_run = false;
}

#ifdef __GNUC__
__attribute__((const))
#endif
int resultExitCode(TResult r) {
    if (r == _ok)
        return OK_EXIT_CODE;
    if (r == _wa)
        return WA_EXIT_CODE;
    if (r == _pe)
        return PE_EXIT_CODE;
    if (r == _fail)
        return FAIL_EXIT_CODE;
    if (r == _dirt)
        return DIRT_EXIT_CODE;
    if (r == _points)
        return POINTS_EXIT_CODE;
    if (r == _unexpected_eof)
#ifdef ENABLE_UNEXPECTED_EOF
        return UNEXPECTED_EOF_EXIT_CODE;
#else
        return PE_EXIT_CODE;
#endif
    if (r >= _partially)
        return PC_BASE_EXIT_CODE + (r - _partially);
    return FAIL_EXIT_CODE;
}

void InStream::textColor(
#if !(defined(ON_WINDOWS) && (!defined(_MSC_VER) || _MSC_VER > 1400)) && defined(__GNUC__)
        __attribute__((unused))
#endif
        WORD color
) {
#if defined(ON_WINDOWS) && (!defined(_MSC_VER) || _MSC_VER > 1400)
    HANDLE handle = GetStdHandle(STD_OUTPUT_HANDLE);
    SetConsoleTextAttribute(handle, color);
#endif
#if !defined(ON_WINDOWS) && defined(__GNUC__)
    if (isatty(2))
    {
        switch (color)
        {
        case LightRed:
            fprintf(stderr, "\033[1;31m");
            break;
        case LightCyan:
            fprintf(stderr, "\033[1;36m");
            break;
        case LightGreen:
            fprintf(stderr, "\033[1;32m");
            break;
        case LightYellow:
            fprintf(stderr, "\033[1;33m");
            break;
        case LightGray:
        default:
            fprintf(stderr, "\033[0m");
        }
    }
#endif
}

#ifdef TESTLIB_THROW_EXIT_EXCEPTION_INSTEAD_OF_EXIT
class exit_exception: public std::exception {
private:
    int exitCode;
public:
    exit_exception(int exitCode): exitCode(exitCode) {}
    int getExitCode() { return exitCode; }
};
#endif

NORETURN void halt(int exitCode) {
#ifdef FOOTER
    InStream::textColor(InStream::LightGray);
    std::fprintf(stderr, "Checker: \"%s\"\n", checkerName.c_str());
    std::fprintf(stderr, "Exit code: %d\n", exitCode);
    InStream::textColor(InStream::LightGray);
#endif
    __testlib_exitCode = exitCode;
#ifdef TESTLIB_THROW_EXIT_EXCEPTION_INSTEAD_OF_EXIT
    throw exit_exception(exitCode);
#endif
    std::exit(exitCode);
}

static bool __testlib_shouldCheckDirt(TResult result) {
    return result == _ok || result == _points || result >= _partially;
}

static std::string __testlib_appendMessage(const std::string &message, const std::string &extra) {
    int openPos = -1, closePos = -1;
    for (size_t i = 0; i < message.length(); i++) {
        if (message[i] == InStream::OPEN_BRACKET) {
            if (openPos == -1)
                openPos = int(i);
            else
                openPos = INT_MAX;
        }
        if (message[i] == InStream::CLOSE_BRACKET) {
            if (closePos == -1)
                closePos = int(i);
            else
                closePos = INT_MAX;
        }
    }
    if (openPos != -1 && openPos != INT_MAX
        && closePos != -1 && closePos != INT_MAX
        && openPos < closePos) {
        size_t index = message.find(extra, openPos);
        if (index == std::string::npos || int(index) >= closePos) {
            std::string result(message);
            result.insert(closePos, ", " + extra);
            return result;
        }
        return message;
    }

    return message + " " + InStream::OPEN_BRACKET + extra + InStream::CLOSE_BRACKET;
}

static std::string __testlib_toPrintableMessage(const std::string &message) {
    int openPos = -1, closePos = -1;
    for (size_t i = 0; i < message.length(); i++) {
        if (message[i] == InStream::OPEN_BRACKET) {
            if (openPos == -1)
                openPos = int(i);
            else
                openPos = INT_MAX;
        }
        if (message[i] == InStream::CLOSE_BRACKET) {
            if (closePos == -1)
                closePos = int(i);
            else
                closePos = INT_MAX;
        }
    }
    if (openPos != -1 && openPos != INT_MAX
        && closePos != -1 && closePos != INT_MAX
        && openPos < closePos) {
        std::string result(message);
        result[openPos] = '(';
        result[closePos] = ')';
        return result;
    }

    return message;
}

NORETURN void InStream::quit(TResult result, const char *msg) {
    if (TestlibFinalizeGuard::alive)
        testlibFinalizeGuard.quitCount++;

    std::string message(msg);
    message = trim(message);

    if (__testlib_hasTestCase) {
        if (result != _ok)
            message = __testlib_appendMessage(message, "test case " + vtos(__testlib_testCase));
        else {
            if (__testlib_testCase == 1)
                message = __testlib_appendMessage(message, vtos(__testlib_testCase) + " test case");
            else
                message = __testlib_appendMessage(message, vtos(__testlib_testCase) + " test cases");
        }
    }

    // You can change maxMessageLength.
    // Example: 'inf.maxMessageLength = 1024 * 1024;'.
    if (message.length() > maxMessageLength) {
        std::string warn = "message length exceeds " + vtos(maxMessageLength)
                           + ", the message is truncated: ";
        message = warn + message.substr(0, maxMessageLength - warn.length());
    }

#ifndef ENABLE_UNEXPECTED_EOF
    if (result == _unexpected_eof)
        result = _pe;
#endif

    if (testlibMode == _scorer && result != _fail)
        quits(_fail, "Scorer should return points only. Don't use a quit function.");

    if (mode != _output && result != _fail) {
        if (mode == _input && testlibMode == _validator && lastLine != -1)
            quits(_fail, __testlib_appendMessage(__testlib_appendMessage(message, name), "line " + vtos(lastLine)));
        else
            quits(_fail, __testlib_appendMessage(message, name));
    }

    std::FILE *resultFile;
    std::string errorName;

    if (__testlib_shouldCheckDirt(result)) {
        if (testlibMode != _interactor && !ouf.seekEof())
            quit(_dirt, "Extra information in the output file");
    }

    int pctype = result - _partially;
    bool isPartial = false;

    switch (result) {
        case _ok:
            errorName = "ok ";
            quitscrS(LightGreen, errorName);
            break;
        case _wa:
            errorName = "wrong answer ";
            quitscrS(LightRed, errorName);
            break;
        case _pe:
            errorName = "wrong output format ";
            quitscrS(LightRed, errorName);
            break;
        case _fail:
            errorName = "FAIL ";
            quitscrS(LightRed, errorName);
            break;
        case _dirt:
            errorName = "wrong output format ";
            quitscrS(LightCyan, errorName);
            result = _pe;
            break;
        case _points:
            errorName = "points ";
            quitscrS(LightYellow, errorName);
            break;
        case _unexpected_eof:
            errorName = "unexpected eof ";
            quitscrS(LightCyan, errorName);
            break;
        default:
            if (result >= _partially) {
                errorName = format("partially correct (%d) ", pctype);
                isPartial = true;
                quitscrS(LightYellow, errorName);
            } else
                quit(_fail, "What is the code ??? ");
    }

    if (resultName != "") {
        resultFile = std::fopen(resultName.c_str(), "w");
        if (resultFile == NULL) {
            resultName = "";
            quit(_fail, "Can not write to the result file");
        }
        if (appesMode) {
            std::fprintf(resultFile, "<?xml version=\"1.0\" encoding=\"%s\"?>", appesModeEncoding.c_str());
            if (isPartial)
                std::fprintf(resultFile, "<result outcome = \"%s\" pctype = \"%d\">",
                             outcomes[(int) _partially].c_str(), pctype);
            else {
                if (result != _points)
                    std::fprintf(resultFile, "<result outcome = \"%s\">", outcomes[(int) result].c_str());
                else {
                    if (__testlib_points == std::numeric_limits<float>::infinity())
                        quit(_fail, "Expected points, but infinity found");
                    std::string stringPoints = removeDoubleTrailingZeroes(format("%.10f", __testlib_points));
                    std::fprintf(resultFile, "<result outcome = \"%s\" points = \"%s\">",
                                 outcomes[(int) result].c_str(), stringPoints.c_str());
                }
            }
            xmlSafeWrite(resultFile, __testlib_toPrintableMessage(message).c_str());
            std::fprintf(resultFile, "</result>\n");
        } else
            std::fprintf(resultFile, "%s", __testlib_toPrintableMessage(message).c_str());
        if (NULL == resultFile || fclose(resultFile) != 0) {
            resultName = "";
            quit(_fail, "Can not write to the result file");
        }
    }

    quitscr(LightGray, __testlib_toPrintableMessage(message).c_str());
    std::fprintf(stderr, "\n");

    inf.close();
    ouf.close();
    ans.close();
    if (tout.is_open())
        tout.close();

    textColor(LightGray);

    if (resultName != "")
        std::fprintf(stderr, "See file to check exit message\n");

    halt(resultExitCode(result));
}

#ifdef __GNUC__
__attribute__ ((format (printf, 3, 4)))
#endif
NORETURN void InStream::quitf(TResult result, const char *msg, ...) {
    FMT_TO_RESULT(msg, msg, message);
    InStream::quit(result, message.c_str());
}

#ifdef __GNUC__
__attribute__ ((format (printf, 4, 5)))
#endif
void InStream::quitif(bool condition, TResult result, const char *msg, ...) {
    if (condition) {
        FMT_TO_RESULT(msg, msg, message);
        InStream::quit(result, message.c_str());
    }
}

NORETURN void InStream::quits(TResult result, std::string msg) {
    InStream::quit(result, msg.c_str());
}

void InStream::xmlSafeWrite(std::FILE *file, const char *msg) {
    size_t lmsg = strlen(msg);
    for (size_t i = 0; i < lmsg; i++) {
        if (msg[i] == '&') {
            std::fprintf(file, "%s", "&amp;");
            continue;
        }
        if (msg[i] == '<') {
            std::fprintf(file, "%s", "&lt;");
            continue;
        }
        if (msg[i] == '>') {
            std::fprintf(file, "%s", "&gt;");
            continue;
        }
        if (msg[i] == '"') {
            std::fprintf(file, "%s", "&quot;");
            continue;
        }
        if (0 <= msg[i] && msg[i] <= 31) {
            std::fprintf(file, "%c", '.');
            continue;
        }
        std::fprintf(file, "%c", msg[i]);
    }
}

void InStream::quitscrS(WORD color, std::string msg) {
    quitscr(color, msg.c_str());
}

void InStream::quitscr(WORD color, const char *msg) {
    if (resultName == "") {
        textColor(color);
        std::fprintf(stderr, "%s", msg);
        textColor(LightGray);
    }
}

void InStream::reset(std::FILE *file) {
    if (opened && stdfile)
        quit(_fail, "Can't reset standard handle");

    if (opened)
        close();

    if (!stdfile && NULL == file)
        if (NULL == (file = std::fopen(name.c_str(), "rb"))) {
            if (mode == _output)
                quits(_pe, std::string("Output file not found: \"") + name + "\"");

            if (mode == _answer)
                quits(_fail, std::string("Answer file not found: \"") + name + "\"");
        }

    if (NULL != file) {
        opened = true;
        __testlib_set_binary(file);

        if (stdfile)
            reader = new FileInputStreamReader(file, name);
        else
            reader = new BufferedFileInputStreamReader(file, name);
    } else {
        opened = false;
        reader = NULL;
    }
}

void InStream::init(std::string fileName, TMode mode) {
    opened = false;
    name = fileName;
    stdfile = false;
    this->mode = mode;

    std::ifstream stream;
    stream.open(fileName.c_str(), std::ios::in);
    if (stream.is_open()) {
        std::streampos start = stream.tellg();
        stream.seekg(0, std::ios::end);
        std::streampos end = stream.tellg();
        size_t fileSize = size_t(end - start);
        stream.close();

        // You can change maxFileSize.
        // Example: 'inf.maxFileSize = 256 * 1024 * 1024;'.
        if (fileSize > maxFileSize)
            quitf(_pe, "File size exceeds %d bytes, size is %d", int(maxFileSize), int(fileSize));
    }

    reset();
}

void InStream::init(std::FILE *f, TMode mode) {
    opened = false;
    name = "untitled";
    this->mode = mode;

    if (f == stdin)
        name = "stdin", stdfile = true;
    if (f == stdout)
        name = "stdout", stdfile = true;
    if (f == stderr)
        name = "stderr", stdfile = true;

    reset(f);
}

void InStream::skipBom() {
    const std::string utf8Bom = "\xEF\xBB\xBF";
    size_t index = 0;
    while (index < utf8Bom.size() && curChar() == utf8Bom[index]) {
        index++;
        skipChar();
    }
    if (index < utf8Bom.size()) {
        while (index != 0) {
            unreadChar(utf8Bom[index - 1]);
            index--;
        }
    }
}

char InStream::curChar() {
    return char(reader->curChar());
}

char InStream::nextChar() {
    return char(reader->nextChar());
}

char InStream::readChar() {
    return nextChar();
}

char InStream::readChar(char c) {
    lastLine = reader->getLine();
    char found = readChar();
    if (c != found) {
        if (!isEoln(found))
            quit(_pe, ("Unexpected character '" + std::string(1, found) + "', but '" + std::string(1, c) +
                       "' expected").c_str());
        else
            quit(_pe, ("Unexpected character " + ("#" + vtos(int(found))) + ", but '" + std::string(1, c) +
                       "' expected").c_str());
    }
    return found;
}

char InStream::readSpace() {
    return readChar(' ');
}

void InStream::unreadChar(char c) {
    reader->unreadChar(c);
}

void InStream::skipChar() {
    reader->skipChar();
}

void InStream::skipBlanks() {
    while (isBlanks(reader->curChar()))
        reader->skipChar();
}

std::string InStream::readWord() {
    readWordTo(_tmpReadToken);
    return _tmpReadToken;
}

void InStream::readWordTo(std::string &result) {
    if (!strict)
        skipBlanks();

    lastLine = reader->getLine();
    int cur = reader->nextChar();

    if (cur == EOFC)
        quit(_unexpected_eof, "Unexpected end of file - token expected");

    if (isBlanks(cur))
        quit(_pe, "Unexpected white-space - token expected");

    result.clear();

    while (!(isBlanks(cur) || cur == EOFC)) {
        result += char(cur);

        // You can change maxTokenLength.
        // Example: 'inf.maxTokenLength = 128 * 1024 * 1024;'.
        if (result.length() > maxTokenLength)
            quitf(_pe, "Length of token exceeds %d, token is '%s...'", int(maxTokenLength),
                  __testlib_part(result).c_str());

        cur = reader->nextChar();
    }

    reader->unreadChar(cur);

    if (result.length() == 0)
        quit(_unexpected_eof, "Unexpected end of file or white-space - token expected");
}

std::string InStream::readToken() {
    return readWord();
}

void InStream::readTokenTo(std::string &result) {
    readWordTo(result);
}

#ifdef __GNUC__
__attribute__((const))
#endif
static std::string __testlib_part(const std::string &s) {
    std::string t;
    for (size_t i = 0; i < s.length(); i++)
        if (s[i] != '\0')
            t += s[i];
        else
            t += '~';
    if (t.length() <= 64)
        return t;
    else
        return t.substr(0, 30) + "..." + t.substr(s.length() - 31, 31);
}

#define __testlib_readMany(readMany, readOne, typeName, space)                  \
    if (size < 0)                                                               \
        quit(_fail, #readMany ": size should be non-negative.");                \
    if (size > 100000000)                                                       \
        quit(_fail, #readMany ": size should be at most 100000000.");           \
                                                                                \
    std::vector<typeName> result(size);                                         \
    readManyIteration = indexBase;                                              \
                                                                                \
    for (int i = 0; i < size; i++)                                              \
    {                                                                           \
        result[i] = readOne;                                                    \
        readManyIteration++;                                                    \
        if (strict && space && i + 1 < size)                                              \
            readSpace();                                                        \
    }                                                                           \
                                                                                \
    readManyIteration = NO_INDEX;                                               \
    return result;                                                              \


std::string InStream::readWord(const pattern &p, const std::string &variableName) {
    readWordTo(_tmpReadToken);
    if (!p.matches(_tmpReadToken)) {
        if (readManyIteration == NO_INDEX) {
            if (variableName.empty())
                quit(_wa,
                     ("Token \"" + __testlib_part(_tmpReadToken) + "\" doesn't correspond to pattern \"" + p.src() +
                      "\"").c_str());
            else
                quit(_wa, ("Token parameter [name=" + variableName + "] equals to \"" + __testlib_part(_tmpReadToken) +
                           "\", doesn't correspond to pattern \"" + p.src() + "\"").c_str());
        } else {
            if (variableName.empty())
                quit(_wa, ("Token element [index=" + vtos(readManyIteration) + "] equals to \"" +
                           __testlib_part(_tmpReadToken) + "\" doesn't correspond to pattern \"" + p.src() +
                           "\"").c_str());
            else
                quit(_wa, ("Token element " + variableName + "[" + vtos(readManyIteration) + "] equals to \"" +
                           __testlib_part(_tmpReadToken) + "\", doesn't correspond to pattern \"" + p.src() +
                           "\"").c_str());
        }
    }
    return _tmpReadToken;
}

std::vector<std::string>
InStream::readWords(int size, const pattern &p, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readWords, readWord(p, variablesName), std::string, true);
}

std::vector<std::string> InStream::readWords(int size, int indexBase) {
    __testlib_readMany(readWords, readWord(), std::string, true);
}

std::string InStream::readWord(const std::string &ptrn, const std::string &variableName) {
    return readWord(pattern(ptrn), variableName);
}

std::vector<std::string>
InStream::readWords(int size, const std::string &ptrn, const std::string &variablesName, int indexBase) {
    pattern p(ptrn);
    __testlib_readMany(readWords, readWord(p, variablesName), std::string, true);
}

std::string InStream::readToken(const pattern &p, const std::string &variableName) {
    return readWord(p, variableName);
}

std::vector<std::string>
InStream::readTokens(int size, const pattern &p, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readTokens, readToken(p, variablesName), std::string, true);
}

std::vector<std::string> InStream::readTokens(int size, int indexBase) {
    __testlib_readMany(readTokens, readToken(), std::string, true);
}

std::string InStream::readToken(const std::string &ptrn, const std::string &variableName) {
    return readWord(ptrn, variableName);
}

std::vector<std::string>
InStream::readTokens(int size, const std::string &ptrn, const std::string &variablesName, int indexBase) {
    pattern p(ptrn);
    __testlib_readMany(readTokens, readWord(p, variablesName), std::string, true);
}

void InStream::readWordTo(std::string &result, const pattern &p, const std::string &variableName) {
    readWordTo(result);
    if (!p.matches(result)) {
        if (variableName.empty())
            quit(_wa, ("Token \"" + __testlib_part(result) + "\" doesn't correspond to pattern \"" + p.src() +
                       "\"").c_str());
        else
            quit(_wa, ("Token parameter [name=" + variableName + "] equals to \"" + __testlib_part(result) +
                       "\", doesn't correspond to pattern \"" + p.src() + "\"").c_str());
    }
}

void InStream::readWordTo(std::string &result, const std::string &ptrn, const std::string &variableName) {
    return readWordTo(result, pattern(ptrn), variableName);
}

void InStream::readTokenTo(std::string &result, const pattern &p, const std::string &variableName) {
    return readWordTo(result, p, variableName);
}

void InStream::readTokenTo(std::string &result, const std::string &ptrn, const std::string &variableName) {
    return readWordTo(result, ptrn, variableName);
}

#ifdef __GNUC__
__attribute__((pure))
#endif
static inline bool equals(long long integer, const char *s) {
    if (integer == LLONG_MIN)
        return strcmp(s, "-9223372036854775808") == 0;

    if (integer == 0LL)
        return strcmp(s, "0") == 0;

    size_t length = strlen(s);

    if (length == 0)
        return false;

    if (integer < 0 && s[0] != '-')
        return false;

    if (integer < 0)
        s++, length--, integer = -integer;

    if (length == 0)
        return false;

    while (integer > 0) {
        int digit = int(integer % 10);

        if (s[length - 1] != '0' + digit)
            return false;

        length--;
        integer /= 10;
    }

    return length == 0;
}

#ifdef __GNUC__
__attribute__((pure))
#endif
static inline bool equals(unsigned long long integer, const char *s) {
    if (integer == ULLONG_MAX)
        return strcmp(s, "18446744073709551615") == 0;

    if (integer == 0ULL)
        return strcmp(s, "0") == 0;

    size_t length = strlen(s);

    if (length == 0)
        return false;

    while (integer > 0) {
        int digit = int(integer % 10);

        if (s[length - 1] != '0' + digit)
            return false;

        length--;
        integer /= 10;
    }

    return length == 0;
}

static inline double stringToDouble(InStream &in, const char *buffer) {
    double result;

    size_t length = strlen(buffer);

    int minusCount = 0;
    int plusCount = 0;
    int decimalPointCount = 0;
    int digitCount = 0;
    int eCount = 0;

    for (size_t i = 0; i < length; i++) {
        if (('0' <= buffer[i] && buffer[i] <= '9') || buffer[i] == '.'
            || buffer[i] == 'e' || buffer[i] == 'E'
            || buffer[i] == '-' || buffer[i] == '+') {
            if ('0' <= buffer[i] && buffer[i] <= '9')
                digitCount++;
            if (buffer[i] == 'e' || buffer[i] == 'E')
                eCount++;
            if (buffer[i] == '-')
                minusCount++;
            if (buffer[i] == '+')
                plusCount++;
            if (buffer[i] == '.')
                decimalPointCount++;
        } else
            in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found").c_str());
    }

    // If for sure is not a number in standard notation or in e-notation.
    if (digitCount == 0 || minusCount > 2 || plusCount > 2 || decimalPointCount > 1 || eCount > 1)
        in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found").c_str());

    char *suffix = new char[length + 1];
    std::memset(suffix, 0, length + 1);
    int scanned = std::sscanf(buffer, "%lf%s", &result, suffix);
    bool empty = strlen(suffix) == 0;
    delete[] suffix;

    if (scanned == 1 || (scanned == 2 && empty)) {
        if (__testlib_isNaN(result))
            in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found").c_str());
        return result;
    } else
        in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found").c_str());
}

static inline double stringToDouble(InStream &in, const std::string& buffer) {
    for (size_t i = 0; i < buffer.length(); i++)
        if (buffer[i] == '\0')
            in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found (it contains \\0)").c_str());
    return stringToDouble(in, buffer.c_str());
}

static inline double stringToStrictDouble(InStream &in, const char *buffer,
        int minAfterPointDigitCount, int maxAfterPointDigitCount) {
    if (minAfterPointDigitCount < 0)
        in.quit(_fail, "stringToStrictDouble: minAfterPointDigitCount should be non-negative.");

    if (minAfterPointDigitCount > maxAfterPointDigitCount)
        in.quit(_fail,
                "stringToStrictDouble: minAfterPointDigitCount should be less or equal to maxAfterPointDigitCount.");

    double result;

    size_t length = strlen(buffer);

    if (length == 0 || length > 1000)
        in.quit(_pe, ("Expected strict double, but \"" + __testlib_part(buffer) + "\" found").c_str());

    if (buffer[0] != '-' && (buffer[0] < '0' || buffer[0] > '9'))
        in.quit(_pe, ("Expected strict double, but \"" + __testlib_part(buffer) + "\" found").c_str());

    int pointPos = -1;
    for (size_t i = 1; i + 1 < length; i++) {
        if (buffer[i] == '.') {
            if (pointPos > -1)
                in.quit(_pe, ("Expected strict double, but \"" + __testlib_part(buffer) + "\" found").c_str());
            pointPos = int(i);
        }
        if (buffer[i] != '.' && (buffer[i] < '0' || buffer[i] > '9'))
            in.quit(_pe, ("Expected strict double, but \"" + __testlib_part(buffer) + "\" found").c_str());
    }

    if (buffer[length - 1] < '0' || buffer[length - 1] > '9')
        in.quit(_pe, ("Expected strict double, but \"" + __testlib_part(buffer) + "\" found").c_str());

    int afterDigitsCount = (pointPos == -1 ? 0 : int(length) - pointPos - 1);
    if (afterDigitsCount < minAfterPointDigitCount || afterDigitsCount > maxAfterPointDigitCount)
        in.quit(_pe, ("Expected strict double with number of digits after point in range ["
                      + vtos(minAfterPointDigitCount)
                      + ","
                      + vtos(maxAfterPointDigitCount)
                      + "], but \"" + __testlib_part(buffer) + "\" found").c_str()
        );

    int firstDigitPos = -1;
    for (size_t i = 0; i < length; i++)
        if (buffer[i] >= '0' && buffer[i] <= '9') {
            firstDigitPos = int(i);
            break;
        }

    if (firstDigitPos > 1 || firstDigitPos == -1)
        in.quit(_pe, ("Expected strict double, but \"" + __testlib_part(buffer) + "\" found").c_str());

    if (buffer[firstDigitPos] == '0' && firstDigitPos + 1 < int(length)
        && buffer[firstDigitPos + 1] >= '0' && buffer[firstDigitPos + 1] <= '9')
        in.quit(_pe, ("Expected strict double, but \"" + __testlib_part(buffer) + "\" found").c_str());

    char *suffix = new char[length + 1];
    std::memset(suffix, 0, length + 1);
    int scanned = std::sscanf(buffer, "%lf%s", &result, suffix);
    bool empty = strlen(suffix) == 0;
    delete[] suffix;

    if (scanned == 1 || (scanned == 2 && empty)) {
        if (__testlib_isNaN(result) || __testlib_isInfinite(result))
            in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found").c_str());
        if (buffer[0] == '-' && result >= 0)
            in.quit(_pe, ("Redundant minus in \"" + __testlib_part(buffer) + "\" found").c_str());
        return result;
    } else
        in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found").c_str());
}

static inline double stringToStrictDouble(InStream &in, const std::string& buffer,
        int minAfterPointDigitCount, int maxAfterPointDigitCount) {
    for (size_t i = 0; i < buffer.length(); i++)
        if (buffer[i] == '\0')
            in.quit(_pe, ("Expected double, but \"" + __testlib_part(buffer) + "\" found (it contains \\0)").c_str());
    return stringToStrictDouble(in, buffer.c_str(), minAfterPointDigitCount, maxAfterPointDigitCount);
}

static inline long long stringToLongLong(InStream &in, const char *buffer) {
    size_t length = strlen(buffer);
    if (length == 0 || length > 20)
        in.quit(_pe, ("Expected integer, but \"" + __testlib_part(buffer) + "\" found").c_str());

    bool has_minus = (length > 1 && buffer[0] == '-');
    int zeroes = 0;
    bool processingZeroes = true;

    for (int i = (has_minus ? 1 : 0); i < int(length); i++) {
        if (buffer[i] == '0' && processingZeroes)
            zeroes++;
        else
            processingZeroes = false;

        if (buffer[i] < '0' || buffer[i] > '9')
            in.quit(_pe, ("Expected integer, but \"" + __testlib_part(buffer) + "\" found").c_str());
    }

    long long int result;
    try {
        result = std::stoll(buffer);
    } catch (const std::exception&) {
        in.quit(_pe, ("Expected integer, but \"" + __testlib_part(buffer) + "\" found").c_str());
    } catch (...) {
        in.quit(_pe, ("Expected integer, but \"" + __testlib_part(buffer) + "\" found").c_str());
    }

    if ((zeroes > 0 && (result != 0 || has_minus)) || zeroes > 1)
        in.quit(_pe, ("Expected integer, but \"" + __testlib_part(buffer) + "\" found").c_str());

    return result;
}

static inline long long stringToLongLong(InStream &in, const std::string& buffer) {
    for (size_t i = 0; i < buffer.length(); i++)
        if (buffer[i] == '\0')
            in.quit(_pe, ("Expected integer, but \"" + __testlib_part(buffer) + "\" found (it contains \\0)").c_str());
    return stringToLongLong(in, buffer.c_str());
}

static inline unsigned long long stringToUnsignedLongLong(InStream &in, const char *buffer) {
    size_t length = strlen(buffer);

    if (length == 0 || length > 20)
        in.quit(_pe, ("Expected unsigned integer, but \"" + __testlib_part(buffer) + "\" found").c_str());
    if (length > 1 && buffer[0] == '0')
        in.quit(_pe, ("Expected unsigned integer, but \"" + __testlib_part(buffer) + "\" found").c_str());

    for (int i = 0; i < int(length); i++) {
        if (buffer[i] < '0' || buffer[i] > '9')
            in.quit(_pe, ("Expected unsigned integer, but \"" + __testlib_part(buffer) + "\" found").c_str());
    }

    unsigned long long result;
    try {
        result = std::stoull(buffer);
    } catch (const std::exception&) {
        in.quit(_pe, ("Expected unsigned integer, but \"" + __testlib_part(buffer) + "\" found").c_str());
    } catch (...) {
        in.quit(_pe, ("Expected unsigned integer, but \"" + __testlib_part(buffer) + "\" found").c_str());
    }

    return result;
}

static inline long long stringToUnsignedLongLong(InStream &in, const std::string& buffer) {
    for (size_t i = 0; i < buffer.length(); i++)
        if (buffer[i] == '\0')
            in.quit(_pe, ("Expected unsigned integer, but \"" + __testlib_part(buffer) + "\" found (it contains \\0)").c_str());
    return stringToUnsignedLongLong(in, buffer.c_str());
}

int InStream::readInteger() {
    if (!strict && seekEof())
        quit(_unexpected_eof, "Unexpected end of file - int32 expected");

    readWordTo(_tmpReadToken);

    long long value = stringToLongLong(*this, _tmpReadToken);
    if (value < INT_MIN || value > INT_MAX)
        quit(_pe, ("Expected int32, but \"" + __testlib_part(_tmpReadToken) + "\" found").c_str());

    return int(value);
}

long long InStream::readLong() {
    if (!strict && seekEof())
        quit(_unexpected_eof, "Unexpected end of file - int64 expected");

    readWordTo(_tmpReadToken);

    return stringToLongLong(*this, _tmpReadToken);
}

unsigned long long InStream::readUnsignedLong() {
    if (!strict && seekEof())
        quit(_unexpected_eof, "Unexpected end of file - int64 expected");

    readWordTo(_tmpReadToken);

    return stringToUnsignedLongLong(*this, _tmpReadToken);
}

long long InStream::readLong(long long minv, long long maxv, const std::string &variableName) {
    long long result = readLong();

    if (result < minv || result > maxv) {
        if (readManyIteration == NO_INDEX) {
            if (variableName.empty())
                quit(_wa, ("Integer " + vtos(result) + " violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) +
                           "]").c_str());
            else
                quit(_wa, ("Integer parameter [name=" + std::string(variableName) + "] equals to " + vtos(result) +
                           ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
        } else {
            if (variableName.empty())
                quit(_wa, ("Integer element [index=" + vtos(readManyIteration) + "] equals to " + vtos(result) +
                           ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
            else
                quit(_wa,
                     ("Integer element " + std::string(variableName) + "[" + vtos(readManyIteration) + "] equals to " +
                      vtos(result) + ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
        }
    }

    if (strict && !variableName.empty())
        validator.addBoundsHit(variableName, ValidatorBoundsHit(minv == result, maxv == result));

    return result;
}

std::vector<long long>
InStream::readLongs(int size, long long minv, long long maxv, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readLongs, readLong(minv, maxv, variablesName), long long, true)
}

std::vector<long long> InStream::readLongs(int size, int indexBase) {
    __testlib_readMany(readLongs, readLong(), long long, true)
}

unsigned long long
InStream::readUnsignedLong(unsigned long long minv, unsigned long long maxv, const std::string &variableName) {
    unsigned long long result = readUnsignedLong();

    if (result < minv || result > maxv) {
        if (readManyIteration == NO_INDEX) {
            if (variableName.empty())
                quit(_wa,
                     ("Unsigned integer " + vtos(result) + " violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) +
                      "]").c_str());
            else
                quit(_wa,
                     ("Unsigned integer parameter [name=" + std::string(variableName) + "] equals to " + vtos(result) +
                      ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
        } else {
            if (variableName.empty())
                quit(_wa,
                     ("Unsigned integer element [index=" + vtos(readManyIteration) + "] equals to " + vtos(result) +
                      ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
            else
                quit(_wa, ("Unsigned integer element " + std::string(variableName) + "[" + vtos(readManyIteration) +
                           "] equals to " + vtos(result) + ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) +
                           "]").c_str());
        }
    }

    if (strict && !variableName.empty())
        validator.addBoundsHit(variableName, ValidatorBoundsHit(minv == result, maxv == result));

    return result;
}

std::vector<unsigned long long> InStream::readUnsignedLongs(int size, unsigned long long minv, unsigned long long maxv,
                                                            const std::string &variablesName, int indexBase) {
    __testlib_readMany(readUnsignedLongs, readUnsignedLong(minv, maxv, variablesName), unsigned long long, true)
}

std::vector<unsigned long long> InStream::readUnsignedLongs(int size, int indexBase) {
    __testlib_readMany(readUnsignedLongs, readUnsignedLong(), unsigned long long, true)
}

unsigned long long
InStream::readLong(unsigned long long minv, unsigned long long maxv, const std::string &variableName) {
    return readUnsignedLong(minv, maxv, variableName);
}

int InStream::readInt() {
    return readInteger();
}

int InStream::readInt(int minv, int maxv, const std::string &variableName) {
    int result = readInt();

    if (result < minv || result > maxv) {
        if (readManyIteration == NO_INDEX) {
            if (variableName.empty())
                quit(_wa, ("Integer " + vtos(result) + " violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) +
                           "]").c_str());
            else
                quit(_wa, ("Integer parameter [name=" + std::string(variableName) + "] equals to " + vtos(result) +
                           ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
        } else {
            if (variableName.empty())
                quit(_wa, ("Integer element [index=" + vtos(readManyIteration) + "] equals to " + vtos(result) +
                           ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
            else
                quit(_wa,
                     ("Integer element " + std::string(variableName) + "[" + vtos(readManyIteration) + "] equals to " +
                      vtos(result) + ", violates the range [" + toHumanReadableString(minv) + ", " + toHumanReadableString(maxv) + "]").c_str());
        }
    }

    if (strict && !variableName.empty())
        validator.addBoundsHit(variableName, ValidatorBoundsHit(minv == result, maxv == result));

    return result;
}

int InStream::readInteger(int minv, int maxv, const std::string &variableName) {
    return readInt(minv, maxv, variableName);
}

std::vector<int> InStream::readInts(int size, int minv, int maxv, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readInts, readInt(minv, maxv, variablesName), int, true)
}

std::vector<int> InStream::readInts(int size, int indexBase) {
    __testlib_readMany(readInts, readInt(), int, true)
}

std::vector<int> InStream::readIntegers(int size, int minv, int maxv, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readIntegers, readInt(minv, maxv, variablesName), int, true)
}

std::vector<int> InStream::readIntegers(int size, int indexBase) {
    __testlib_readMany(readIntegers, readInt(), int, true)
}

double InStream::readReal() {
    if (!strict && seekEof())
        quit(_unexpected_eof, "Unexpected end of file - double expected");

    return stringToDouble(*this, readWord());
}

double InStream::readDouble() {
    return readReal();
}

double InStream::readReal(double minv, double maxv, const std::string &variableName) {
    double result = readReal();

    if (result < minv || result > maxv) {
        if (readManyIteration == NO_INDEX) {
            if (variableName.empty())
                quit(_wa, ("Double " + vtos(result) + " violates the range [" + vtos(minv) + ", " + vtos(maxv) +
                           "]").c_str());
            else
                quit(_wa, ("Double parameter [name=" + std::string(variableName) + "] equals to " + vtos(result) +
                           ", violates the range [" + vtos(minv) + ", " + vtos(maxv) + "]").c_str());
        } else {
            if (variableName.empty())
                quit(_wa, ("Double element [index=" + vtos(readManyIteration) + "] equals to " + vtos(result) +
                           ", violates the range [" + vtos(minv) + ", " + vtos(maxv) + "]").c_str());
            else
                quit(_wa,
                     ("Double element " + std::string(variableName) + "[" + vtos(readManyIteration) + "] equals to " +
                      vtos(result) + ", violates the range [" + vtos(minv) + ", " + vtos(maxv) + "]").c_str());
        }
    }

    if (strict && !variableName.empty())
        validator.addBoundsHit(variableName, ValidatorBoundsHit(
                doubleDelta(minv, result) < ValidatorBoundsHit::EPS,
                doubleDelta(maxv, result) < ValidatorBoundsHit::EPS
        ));

    return result;
}

std::vector<double>
InStream::readReals(int size, double minv, double maxv, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readReals, readReal(minv, maxv, variablesName), double, true)
}

std::vector<double> InStream::readReals(int size, int indexBase) {
    __testlib_readMany(readReals, readReal(), double, true)
}

double InStream::readDouble(double minv, double maxv, const std::string &variableName) {
    return readReal(minv, maxv, variableName);
}

std::vector<double>
InStream::readDoubles(int size, double minv, double maxv, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readDoubles, readDouble(minv, maxv, variablesName), double, true)
}

std::vector<double> InStream::readDoubles(int size, int indexBase) {
    __testlib_readMany(readDoubles, readDouble(), double, true)
}

double InStream::readStrictReal(double minv, double maxv,
                                int minAfterPointDigitCount, int maxAfterPointDigitCount,
                                const std::string &variableName) {
    if (!strict && seekEof())
        quit(_unexpected_eof, "Unexpected end of file - strict double expected");

    double result = stringToStrictDouble(*this, readWord(), minAfterPointDigitCount, maxAfterPointDigitCount);

    if (result < minv || result > maxv) {
        if (readManyIteration == NO_INDEX) {
            if (variableName.empty())
                quit(_wa, ("Strict double " + vtos(result) + " violates the range [" + vtos(minv) + ", " + vtos(maxv) +
                           "]").c_str());
            else
                quit(_wa,
                     ("Strict double parameter [name=" + std::string(variableName) + "] equals to " + vtos(result) +
                      ", violates the range [" + vtos(minv) + ", " + vtos(maxv) + "]").c_str());
        } else {
            if (variableName.empty())
                quit(_wa, ("Strict double element [index=" + vtos(readManyIteration) + "] equals to " + vtos(result) +
                           ", violates the range [" + vtos(minv) + ", " + vtos(maxv) + "]").c_str());
            else
                quit(_wa, ("Strict double element " + std::string(variableName) + "[" + vtos(readManyIteration) +
                           "] equals to " + vtos(result) + ", violates the range [" + vtos(minv) + ", " + vtos(maxv) +
                           "]").c_str());
        }
    }

    if (strict && !variableName.empty())
        validator.addBoundsHit(variableName, ValidatorBoundsHit(
                doubleDelta(minv, result) < ValidatorBoundsHit::EPS,
                doubleDelta(maxv, result) < ValidatorBoundsHit::EPS
        ));

    return result;
}

std::vector<double> InStream::readStrictReals(int size, double minv, double maxv,
                                              int minAfterPointDigitCount, int maxAfterPointDigitCount,
                                              const std::string &variablesName, int indexBase) {
    __testlib_readMany(readStrictReals,
                       readStrictReal(minv, maxv, minAfterPointDigitCount, maxAfterPointDigitCount, variablesName),
                       double, true)
}

double InStream::readStrictDouble(double minv, double maxv,
                                  int minAfterPointDigitCount, int maxAfterPointDigitCount,
                                  const std::string &variableName) {
    return readStrictReal(minv, maxv,
                          minAfterPointDigitCount, maxAfterPointDigitCount,
                          variableName);
}

std::vector<double> InStream::readStrictDoubles(int size, double minv, double maxv,
                                                int minAfterPointDigitCount, int maxAfterPointDigitCount,
                                                const std::string &variablesName, int indexBase) {
    __testlib_readMany(readStrictDoubles,
                       readStrictDouble(minv, maxv, minAfterPointDigitCount, maxAfterPointDigitCount, variablesName),
                       double, true)
}

bool InStream::eof() {
    if (!strict && NULL == reader)
        return true;

    return reader->eof();
}

bool InStream::seekEof() {
    if (!strict && NULL == reader)
        return true;
    skipBlanks();
    return eof();
}

bool InStream::eoln() {
    if (!strict && NULL == reader)
        return true;

    int c = reader->nextChar();

    if (!strict) {
        if (c == EOFC)
            return true;

        if (c == CR) {
            c = reader->nextChar();

            if (c != LF) {
                reader->unreadChar(c);
                reader->unreadChar(CR);
                return false;
            } else
                return true;
        }

        if (c == LF)
            return true;

        reader->unreadChar(c);
        return false;
    } else {
        bool returnCr = false;

#if (defined(ON_WINDOWS) && !defined(FOR_LINUX)) || defined(FOR_WINDOWS)
        if (c != CR) {
            reader->unreadChar(c);
            return false;
        } else {
            if (!returnCr)
                returnCr = true;
            c = reader->nextChar();
        }
#endif
        if (c != LF) {
            reader->unreadChar(c);
            if (returnCr)
                reader->unreadChar(CR);
            return false;
        }

        return true;
    }
}

void InStream::readEoln() {
    lastLine = reader->getLine();
    if (!eoln())
        quit(_pe, "Expected EOLN");
}

void InStream::readEof() {
    lastLine = reader->getLine();
    if (!eof())
        quit(_pe, "Expected EOF");

    if (TestlibFinalizeGuard::alive && this == &inf)
        testlibFinalizeGuard.readEofCount++;
}

bool InStream::seekEoln() {
    if (!strict && NULL == reader)
        return true;

    int cur;
    do {
        cur = reader->nextChar();
    } while (cur == SPACE || cur == TAB);

    reader->unreadChar(cur);
    return eoln();
}

void InStream::nextLine() {
    readLine();
}

void InStream::readStringTo(std::string &result) {
    if (NULL == reader)
        quit(_pe, "Expected line");

    result.clear();

    for (;;) {
        int cur = reader->curChar();

        if (cur == LF || cur == EOFC)
            break;

        if (cur == CR) {
            cur = reader->nextChar();
            if (reader->curChar() == LF) {
                reader->unreadChar(cur);
                break;
            }
        }

        lastLine = reader->getLine();
        result += char(reader->nextChar());
    }

    if (strict)
        readEoln();
    else
        eoln();
}

std::string InStream::readString() {
    readStringTo(_tmpReadToken);
    return _tmpReadToken;
}

std::vector<std::string> InStream::readStrings(int size, int indexBase) {
    __testlib_readMany(readStrings, readString(), std::string, false)
}

void InStream::readStringTo(std::string &result, const pattern &p, const std::string &variableName) {
    readStringTo(result);
    if (!p.matches(result)) {
        if (readManyIteration == NO_INDEX) {
            if (variableName.empty())
                quit(_wa, ("Line \"" + __testlib_part(result) + "\" doesn't correspond to pattern \"" + p.src() +
                           "\"").c_str());
            else
                quit(_wa, ("Line [name=" + variableName + "] equals to \"" + __testlib_part(result) +
                           "\", doesn't correspond to pattern \"" + p.src() + "\"").c_str());
        } else {
            if (variableName.empty())
                quit(_wa,
                     ("Line element [index=" + vtos(readManyIteration) + "] equals to \"" + __testlib_part(result) +
                      "\" doesn't correspond to pattern \"" + p.src() + "\"").c_str());
            else
                quit(_wa,
                     ("Line element " + std::string(variableName) + "[" + vtos(readManyIteration) + "] equals to \"" +
                      __testlib_part(result) + "\", doesn't correspond to pattern \"" + p.src() + "\"").c_str());
        }
    }
}

void InStream::readStringTo(std::string &result, const std::string &ptrn, const std::string &variableName) {
    readStringTo(result, pattern(ptrn), variableName);
}

std::string InStream::readString(const pattern &p, const std::string &variableName) {
    readStringTo(_tmpReadToken, p, variableName);
    return _tmpReadToken;
}

std::vector<std::string>
InStream::readStrings(int size, const pattern &p, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readStrings, readString(p, variablesName), std::string, false)
}

std::string InStream::readString(const std::string &ptrn, const std::string &variableName) {
    readStringTo(_tmpReadToken, ptrn, variableName);
    return _tmpReadToken;
}

std::vector<std::string>
InStream::readStrings(int size, const std::string &ptrn, const std::string &variablesName, int indexBase) {
    pattern p(ptrn);
    __testlib_readMany(readStrings, readString(p, variablesName), std::string, false)
}

void InStream::readLineTo(std::string &result) {
    readStringTo(result);
}

std::string InStream::readLine() {
    return readString();
}

std::vector<std::string> InStream::readLines(int size, int indexBase) {
    __testlib_readMany(readLines, readString(), std::string, false)
}

void InStream::readLineTo(std::string &result, const pattern &p, const std::string &variableName) {
    readStringTo(result, p, variableName);
}

void InStream::readLineTo(std::string &result, const std::string &ptrn, const std::string &variableName) {
    readStringTo(result, ptrn, variableName);
}

std::string InStream::readLine(const pattern &p, const std::string &variableName) {
    return readString(p, variableName);
}

std::vector<std::string>
InStream::readLines(int size, const pattern &p, const std::string &variablesName, int indexBase) {
    __testlib_readMany(readLines, readString(p, variablesName), std::string, false)
}

std::string InStream::readLine(const std::string &ptrn, const std::string &variableName) {
    return readString(ptrn, variableName);
}

std::vector<std::string>
InStream::readLines(int size, const std::string &ptrn, const std::string &variablesName, int indexBase) {
    pattern p(ptrn);
    __testlib_readMany(readLines, readString(p, variablesName), std::string, false)
}

#ifdef __GNUC__
__attribute__ ((format (printf, 3, 4)))
#endif
void InStream::ensuref(bool cond, const char *format, ...) {
    if (!cond) {
        FMT_TO_RESULT(format, format, message);
        this->__testlib_ensure(cond, message);
    }
}

void InStream::__testlib_ensure(bool cond, std::string message) {
    if (!cond)
        this->quit(_wa, message.c_str());
}

void InStream::close() {
    if (NULL != reader) {
        reader->close();
        delete reader;
        reader = NULL;
    }

    opened = false;
}

NORETURN void quit(TResult result, const std::string &msg) {
    ouf.quit(result, msg.c_str());
}

NORETURN void quit(TResult result, const char *msg) {
    ouf.quit(result, msg);
}

NORETURN void __testlib_quitp(double points, const char *message) {
    __testlib_points = points;
    std::string stringPoints = removeDoubleTrailingZeroes(format("%.10f", points));

    std::string quitMessage;
    if (NULL == message || 0 == strlen(message))
        quitMessage = stringPoints;
    else
        quitMessage = stringPoints + " " + message;

    quit(_points, quitMessage.c_str());
}

NORETURN void __testlib_quitp(int points, const char *message) {
    __testlib_points = points;
    std::string stringPoints = format("%d", points);

    std::string quitMessage;
    if (NULL == message || 0 == strlen(message))
        quitMessage = stringPoints;
    else
        quitMessage = stringPoints + " " + message;

    quit(_points, quitMessage.c_str());
}

NORETURN void quitp(float points, const std::string &message = "") {
    __testlib_quitp(double(points), message.c_str());
}

NORETURN void quitp(double points, const std::string &message = "") {
    __testlib_quitp(points, message.c_str());
}

NORETURN void quitp(long double points, const std::string &message = "") {
    __testlib_quitp(double(points), message.c_str());
}

NORETURN void quitp(int points, const std::string &message = "") {
    __testlib_quitp(points, message.c_str());
}

NORETURN void quitpi(const std::string &points_info, const std::string &message = "") {
    if (points_info.find(' ') != std::string::npos)
        quit(_fail, "Parameter 'points_info' can't contain spaces");
    if (message.empty())
        quit(_points, ("points_info=" + points_info).c_str());
    else
        quit(_points, ("points_info=" + points_info + " " + message).c_str());
}

template<typename F>
#ifdef __GNUC__
__attribute__ ((format (printf, 2, 3)))
#endif
NORETURN void quitp(F points, const char *format, ...) {
    FMT_TO_RESULT(format, format, message);
    quitp(points, message);
}

#ifdef __GNUC__
__attribute__ ((format (printf, 2, 3)))
#endif
NORETURN void quitf(TResult result, const char *format, ...) {
    FMT_TO_RESULT(format, format, message);
    quit(result, message);
}

#ifdef __GNUC__
__attribute__ ((format (printf, 3, 4)))
#endif
void quitif(bool condition, TResult result, const char *format, ...) {
    if (condition) {
        FMT_TO_RESULT(format, format, message);
        quit(result, message);
    }
}

NORETURN void __testlib_help() {
    InStream::textColor(InStream::LightCyan);
    std::fprintf(stderr, "TESTLIB %s, https://github.com/MikeMirzayanov/testlib/ ", VERSION);
    std::fprintf(stderr, "by Mike Mirzayanov, copyright(c) 2005-2020\n");
    std::fprintf(stderr, "Checker name: \"%s\"\n", checkerName.c_str());
    InStream::textColor(InStream::LightGray);

    std::fprintf(stderr, "\n");
    std::fprintf(stderr, "Latest features: \n");
    for (size_t i = 0; i < sizeof(latestFeatures) / sizeof(char *); i++) {
        std::fprintf(stderr, "*) %s\n", latestFeatures[i]);
    }
    std::fprintf(stderr, "\n");

    std::fprintf(stderr, "Program must be run with the following arguments: \n");
    std::fprintf(stderr, "    [--testset testset] [--group group] <input-file> <output-file> <answer-file> [<report-file> [<-appes>]]\n\n");

    __testlib_exitCode = FAIL_EXIT_CODE;
    std::exit(FAIL_EXIT_CODE);
}

static void __testlib_ensuresPreconditions() {
    // testlib assumes: sizeof(int) = 4.
    __TESTLIB_STATIC_ASSERT(sizeof(int) == 4);

    // testlib assumes: INT_MAX == 2147483647.
    __TESTLIB_STATIC_ASSERT(INT_MAX == 2147483647);

    // testlib assumes: sizeof(long long) = 8.
    __TESTLIB_STATIC_ASSERT(sizeof(long long) == 8);

    // testlib assumes: sizeof(double) = 8.
    __TESTLIB_STATIC_ASSERT(sizeof(double) == 8);

    // testlib assumes: no -ffast-math.
    if (!__testlib_isNaN(+__testlib_nan()))
        quit(_fail, "Function __testlib_isNaN is not working correctly: possible reason is '-ffast-math'");
    if (!__testlib_isNaN(-__testlib_nan()))
        quit(_fail, "Function __testlib_isNaN is not working correctly: possible reason is '-ffast-math'");
}

std::string __testlib_testset;

std::string getTestset() {
    return __testlib_testset;
}

std::string __testlib_group;

std::string getGroup() {
    return __testlib_group;
}

static void __testlib_set_testset_and_group(int argc, char* argv[]) {
    for (int i = 1; i < argc; i++) {
        if (!strcmp("--testset", argv[i])) {
            if (i + 1 < argc && strlen(argv[i + 1]) > 0)
                __testlib_testset = argv[++i];
            else
                quit(_fail, std::string("Expected non-empty testset after --testset command line parameter"));
        } else if (!strcmp("--group", argv[i])) {
            if (i + 1 < argc)
                __testlib_group = argv[++i];
            else
                quit(_fail, std::string("Expected group after --group command line parameter"));
        }
    }
}

void registerGen(int argc, char *argv[], int randomGeneratorVersion) {
    if (randomGeneratorVersion < 0 || randomGeneratorVersion > 1)
        quitf(_fail, "Random generator version is expected to be 0 or 1.");
    random_t::version = randomGeneratorVersion;

    __testlib_ensuresPreconditions();
    TestlibFinalizeGuard::registered = true;

    testlibMode = _generator;
    __testlib_set_binary(stdin);
    rnd.setSeed(argc, argv);

#if __cplusplus > 199711L || defined(_MSC_VER)
    prepareOpts(argc, argv);
#endif
}

#ifdef USE_RND_AS_BEFORE_087
void registerGen(int argc, char* argv[])
{
    registerGen(argc, argv, 0);
}
#else
#ifdef __GNUC__
#if (__GNUC__ > 4) || ((__GNUC__ == 4) && (__GNUC_MINOR__ > 4))
__attribute__ ((deprecated("Use registerGen(argc, argv, 0) or registerGen(argc, argv, 1)."
" The third parameter stands for the random generator version."
" If you are trying to compile old generator use macro -DUSE_RND_AS_BEFORE_087 or registerGen(argc, argv, 0)."
" Version 1 has been released on Spring, 2013. Use it to write new generators.")))
#else
__attribute__ ((deprecated))
#endif
#endif
#ifdef _MSC_VER
__declspec(deprecated("Use registerGen(argc, argv, 0) or registerGen(argc, argv, 1)."
        " The third parameter stands for the random generator version."
        " If you are trying to compile old generator use macro -DUSE_RND_AS_BEFORE_087 or registerGen(argc, argv, 0)."
        " Version 1 has been released on Spring, 2013. Use it to write new generators."))
#endif
void registerGen(int argc, char *argv[]) {
    std::fprintf(stderr, "Use registerGen(argc, argv, 0) or registerGen(argc, argv, 1)."
                         " The third parameter stands for the random generator version."
                         " If you are trying to compile old generator use macro -DUSE_RND_AS_BEFORE_087 or registerGen(argc, argv, 0)."
                         " Version 1 has been released on Spring, 2013. Use it to write new generators.\n\n");
    registerGen(argc, argv, 0);
}
#endif

void setAppesModeEncoding(std::string appesModeEncoding) {
    static const char* const ENCODINGS[] = {"ascii", "utf-7", "utf-8", "utf-16", "utf-16le", "utf-16be", "utf-32", "utf-32le", "utf-32be", "iso-8859-1", 
"iso-8859-2", "iso-8859-3", "iso-8859-4", "iso-8859-5", "iso-8859-6", "iso-8859-7", "iso-8859-8", "iso-8859-9", "iso-8859-10", "iso-8859-11", 
"iso-8859-13", "iso-8859-14", "iso-8859-15", "iso-8859-16", "windows-1250", "windows-1251", "windows-1252", "windows-1253", "windows-1254", "windows-1255", 
"windows-1256", "windows-1257", "windows-1258", "gb2312", "gbk", "gb18030", "big5", "shift-jis", "euc-jp", "euc-kr", 
"euc-cn", "euc-tw", "koi8-r", "koi8-u", "tis-620", "ibm437", "ibm850", "ibm852", "ibm855", "ibm857", 
"ibm860", "ibm861", "ibm862", "ibm863", "ibm865", "ibm866", "ibm869", "macroman", "maccentraleurope", "maciceland", 
"maccroatian", "macromania", "maccyrillic", "macukraine", "macgreek", "macturkish", "machebrew", "macarabic", "macthai", "hz-gb-2312", 
"iso-2022-jp", "iso-2022-kr", "iso-2022-cn", "armscii-8", "tscii", "iscii", "viscii", "geostd8", "cp949", "cp874", 
"cp1006", "cp775", "cp858", "cp737", "cp853", "cp856", "cp922", "cp1046", "cp1125", "cp1131", 
"ptcp154", "koi8-t", "koi8-ru", "mulelao-1", "cp1133", "iso-ir-166", "tcvn", "iso-ir-14", "iso-ir-87", "iso-ir-159"};
    
    appesModeEncoding = lowerCase(appesModeEncoding);
    bool valid = false;
    for (size_t i = 0; i < sizeof(ENCODINGS) / sizeof(ENCODINGS[0]); i++)
        if (appesModeEncoding == ENCODINGS[i]) {
            valid = true;
            break;
        }
    if (!valid)
        quit(_fail, "Unexpected encoding for setAppesModeEncoding(encoding)");
    ::appesModeEncoding = appesModeEncoding;
}

void registerInteraction(int argc, char *argv[]) {
    __testlib_ensuresPreconditions();
    __testlib_set_testset_and_group(argc, argv);
    TestlibFinalizeGuard::registered = true;

    testlibMode = _interactor;
    __testlib_set_binary(stdin);

    if (argc > 1 && !strcmp("--help", argv[1]))
        __testlib_help();

    if (argc < 3 || argc > 6) {
        quit(_fail, std::string("Program must be run with the following arguments: ") +
                    std::string("<input-file> <output-file> [<answer-file> [<report-file> [<-appes>]]]") +
                    "\nUse \"--help\" to get help information");
    }

    if (argc <= 4) {
        resultName = "";
        appesMode = false;
    }

#ifndef EJUDGE
    if (argc == 5) {
        resultName = argv[4];
        appesMode = false;
    }

    if (argc == 6) {
        if (strcmp("-APPES", argv[5]) && strcmp("-appes", argv[5])) {
            quit(_fail, std::string("Program must be run with the following arguments: ") +
                        "<input-file> <output-file> <answer-file> [<report-file> [<-appes>]]");
        } else {
            resultName = argv[4];
            appesMode = true;
        }
    }
#endif

    inf.init(argv[1], _input);

    tout.open(argv[2], std::ios_base::out);
    if (tout.fail() || !tout.is_open())
        quit(_fail, std::string("Can not write to the test-output-file '") + argv[2] + std::string("'"));

    ouf.init(stdin, _output);

    if (argc >= 4)
        ans.init(argv[3], _answer);
    else
        ans.name = "unopened answer stream";
}

void registerValidation() {
    __testlib_ensuresPreconditions();
    TestlibFinalizeGuard::registered = true;

    testlibMode = _validator;

    __testlib_set_binary(stdin);
    __testlib_set_binary(stdout);
    __testlib_set_binary(stderr);

    inf.init(stdin, _input);
    inf.strict = true;
}

void registerValidation(int argc, char *argv[]) {
    registerValidation();
    __testlib_set_testset_and_group(argc, argv);

    validator.initialize();
    TestlibFinalizeGuard::registered = true;

    std::string comment = "Validator must be run with the following arguments:"
                            " [--testset testset]"
                            " [--group group]"
                            " [--testOverviewLogFileName fileName]"
                            " [--testMarkupFileName fileName]"
                            " [--testCase testCase]"
                            " [--testCaseFileName fileName]"
                            ;

    for (int i = 1; i < argc; i++) {
        if (!strcmp("--testset", argv[i])) {
            if (i + 1 < argc && strlen(argv[i + 1]) > 0)
                validator.setTestset(argv[++i]);
            else
                quit(_fail, comment);
        }
        if (!strcmp("--group", argv[i])) {
            if (i + 1 < argc)
                validator.setGroup(argv[++i]);
            else
                quit(_fail, comment);
        }
        if (!strcmp("--testOverviewLogFileName", argv[i])) {
            if (i + 1 < argc)
                validator.setTestOverviewLogFileName(argv[++i]);
            else
                quit(_fail, comment);
        }
        if (!strcmp("--testMarkupFileName", argv[i])) {
            if (i + 1 < argc)
                validator.setTestMarkupFileName(argv[++i]);
            else
                quit(_fail, comment);
        }
        if (!strcmp("--testCase", argv[i])) {
            if (i + 1 < argc) {
                long long testCase = stringToLongLong(inf, argv[++i]);
                if (testCase < 1 || testCase >= __TESTLIB_MAX_TEST_CASE)
                    quit(_fail, format("Argument testCase should be between 1 and %d, but ", __TESTLIB_MAX_TEST_CASE)
                        + toString(testCase) + " found");
                validator.setTestCase(int(testCase));
            } else
                quit(_fail, comment);
        }
        if (!strcmp("--testCaseFileName", argv[i])) {
            if (i + 1 < argc) {
                validator.setTestCaseFileName(argv[++i]);
            } else
                quit(_fail, comment);
        }
    }
}

void addFeature(const std::string &feature) {
    if (testlibMode != _validator)
        quit(_fail, "Features are supported in validators only.");
    validator.addFeature(feature);
}

void feature(const std::string &feature) {
    if (testlibMode != _validator)
        quit(_fail, "Features are supported in validators only.");
    validator.feature(feature);
}

class Checker {
private:
    bool _initialized;
    std::string _testset;
    std::string _group;

public:
    Checker() : _initialized(false), _testset("tests"), _group() {
    }

    void initialize() {
        _initialized = true;
    }

    std::string testset() const {
        if (!_initialized)
            __testlib_fail("Checker should be initialized with registerTestlibCmd(argc, argv) instead of registerTestlibCmd() to support checker.testset()");
        return _testset;
    }

    std::string group() const {
        if (!_initialized)
            __testlib_fail("Checker should be initialized with registerTestlibCmd(argc, argv) instead of registerTestlibCmd() to support checker.group()");
        return _group;
    }

    void setTestset(const char *const testset) {
        _testset = testset;
    }

    void setGroup(const char *const group) {
        _group = group;
    }
} checker;

void registerTestlibCmd(int argc, char *argv[]) {
    __testlib_ensuresPreconditions();
    __testlib_set_testset_and_group(argc, argv);
    TestlibFinalizeGuard::registered = true;

    testlibMode = _checker;
    __testlib_set_binary(stdin);

    std::vector<std::string> args(1, argv[0]);
    checker.initialize();

    for (int i = 1; i < argc; i++) {
        if (!strcmp("--testset", argv[i])) {
            if (i + 1 < argc && strlen(argv[i + 1]) > 0)
                checker.setTestset(argv[++i]);
            else
                quit(_fail, std::string("Expected testset after --testset command line parameter"));
        } else if (!strcmp("--group", argv[i])) {
            if (i + 1 < argc)
                checker.setGroup(argv[++i]);
            else
                quit(_fail, std::string("Expected group after --group command line parameter"));
        } else
            args.push_back(argv[i]);
    }

    argc = int(args.size());
    if (argc > 1 && "--help" == args[1])
        __testlib_help();

    if (argc < 4 || argc > 6) {
        quit(_fail, std::string("Program must be run with the following arguments: ") +
                    std::string("[--testset testset] [--group group] <input-file> <output-file> <answer-file> [<report-file> [<-appes>]]") +
                    "\nUse \"--help\" to get help information");
    }

    if (argc == 4) {
        resultName = "";
        appesMode = false;
    }

#ifndef EJUDGE
    if (argc == 5) {
        resultName = args[4];
        appesMode = false;
    }

    if (argc == 6) {
        if ("-APPES" != args[5] && "-appes" != args[5]) {
            quit(_fail, std::string("Program must be run with the following arguments: ") +
                        "<input-file> <output-file> <answer-file> [<report-file> [<-appes>]]");
        } else {
            resultName = args[4];
            appesMode = true;
        }
    }
#endif

    inf.init(args[1], _input);
    ouf.init(args[2], _output);
    ouf.skipBom();
    ans.init(args[3], _answer);
}

void registerTestlib(int argc, ...) {
    if (argc < 3 || argc > 5)
        quit(_fail, std::string("Program must be run with the following arguments: ") +
                    "<input-file> <output-file> <answer-file> [<report-file> [<-appes>]]");

    char **argv = new char *[argc + 1];

    va_list ap;
    va_start(ap, argc);
    argv[0] = NULL;
    for (int i = 0; i < argc; i++) {
        argv[i + 1] = va_arg(ap, char*);
    }
    va_end(ap);

    registerTestlibCmd(argc + 1, argv);
    delete[] argv;
}

static inline void __testlib_ensure(bool cond, const std::string &msg) {
    if (!cond)
        quit(_fail, msg.c_str());
}

#ifdef __GNUC__
__attribute__((unused))
#endif
static inline void __testlib_ensure(bool cond, const char *msg) {
    if (!cond)
        quit(_fail, msg);
}

#define ensure(cond) __testlib_ensure(cond, "Condition failed: \"" #cond "\"")
#define STRINGIZE_DETAIL(x) #x
#define STRINGIZE(x) STRINGIZE_DETAIL(x)
#define ensure_ext(cond) __testlib_ensure(cond, "Line " STRINGIZE(__LINE__) ": Condition failed: \"" #cond "\"")

#ifdef __GNUC__
__attribute__ ((format (printf, 2, 3)))
#endif
inline void ensuref(bool cond, const char *format, ...) {
    if (!cond) {
        FMT_TO_RESULT(format, format, message);
        __testlib_ensure(cond, message);
    }
}

NORETURN static void __testlib_fail(const std::string &message) {
    quitf(_fail, "%s", message.c_str());
}

#ifdef __GNUC__
__attribute__ ((format (printf, 1, 2)))
#endif
void setName(const char *format, ...) {
    FMT_TO_RESULT(format, format, name);
    checkerName = name;
}

/*
 * Do not use random_shuffle, because it will produce different result
 * for different C++ compilers.
 *
 * This implementation uses testlib random_t to produce random numbers, so
 * it is stable.
 */
template<typename _RandomAccessIter>
void shuffle(_RandomAccessIter __first, _RandomAccessIter __last) {
    if (__first == __last) return;
    for (_RandomAccessIter __i = __first + 1; __i != __last; ++__i)
        std::iter_swap(__i, __first + rnd.next(int(__i - __first) + 1));
}


template<typename _RandomAccessIter>
#if defined(__GNUC__) && !defined(__clang__)
__attribute__ ((error("Don't use random_shuffle(), use shuffle() instead")))
#endif
void random_shuffle(_RandomAccessIter, _RandomAccessIter) {
    quitf(_fail, "Don't use random_shuffle(), use shuffle() instead");
}

#ifdef __GLIBC__
#  define RAND_THROW_STATEMENT throw()
#else
#  define RAND_THROW_STATEMENT
#endif

#if defined(__GNUC__) && !defined(__clang__)

__attribute__ ((error("Don't use rand(), use rnd.next() instead")))
#endif
#ifdef _MSC_VER
#   pragma warning( disable : 4273 )
#endif
int rand() RAND_THROW_STATEMENT
{
    quitf(_fail, "Don't use rand(), use rnd.next() instead");

    /* This line never runs. */
    //throw "Don't use rand(), use rnd.next() instead";
}

#if defined(__GNUC__) && !defined(__clang__)

__attribute__ ((error("Don't use srand(), you should use "
"'registerGen(argc, argv, 1);' to initialize generator seed "
"by hash code of the command line params. The third parameter "
"is randomGeneratorVersion (currently the latest is 1).")))
#endif
#ifdef _MSC_VER
#   pragma warning( disable : 4273 )
#endif
void srand(unsigned int seed) RAND_THROW_STATEMENT
{
    quitf(_fail, "Don't use srand(), you should use "
                 "'registerGen(argc, argv, 1);' to initialize generator seed "
                 "by hash code of the command line params. The third parameter "
                 "is randomGeneratorVersion (currently the latest is 1) [ignored seed=%u].", seed);
}

void startTest(int test) {
    const std::string testFileName = vtos(test);
    if (NULL == freopen(testFileName.c_str(), "wt", stdout))
        __testlib_fail("Unable to write file '" + testFileName + "'");
}

#ifdef __GNUC__
__attribute__((const))
#endif
inline std::string compress(const std::string &s) {
    return __testlib_part(s);
}

#ifdef __GNUC__
__attribute__((const))
#endif
inline std::string englishEnding(int x) {
    x %= 100;
    if (x / 10 == 1)
        return "th";
    if (x % 10 == 1)
        return "st";
    if (x % 10 == 2)
        return "nd";
    if (x % 10 == 3)
        return "rd";
    return "th";
}

template<typename _ForwardIterator, typename _Separator>
#ifdef __GNUC__
__attribute__((const))
#endif
std::string join(_ForwardIterator first, _ForwardIterator last, _Separator separator) {
    std::stringstream ss;
    bool repeated = false;
    for (_ForwardIterator i = first; i != last; i++) {
        if (repeated)
            ss << separator;
        else
            repeated = true;
        ss << *i;
    }
    return ss.str();
}

template<typename _ForwardIterator>
#ifdef __GNUC__
__attribute__((const))
#endif
std::string join(_ForwardIterator first, _ForwardIterator last) {
    return join(first, last, ' ');
}

template<typename _Collection, typename _Separator>
#ifdef __GNUC__
__attribute__((const))
#endif
std::string join(const _Collection &collection, _Separator separator) {
    return join(collection.begin(), collection.end(), separator);
}

template<typename _Collection>
#ifdef __GNUC__
__attribute__((const))
#endif
std::string join(const _Collection &collection) {
    return join(collection, ' ');
}

/**
 * Splits string s by character separator returning exactly k+1 items,
 * where k is the number of separator occurrences.
 */
#ifdef __GNUC__
__attribute__((const))
#endif
std::vector<std::string> split(const std::string &s, char separator) {
    std::vector<std::string> result;
    std::string item;
    for (size_t i = 0; i < s.length(); i++)
        if (s[i] == separator) {
            result.push_back(item);
            item = "";
        } else
            item += s[i];
    result.push_back(item);
    return result;
}

/**
 * Splits string s by character separators returning exactly k+1 items,
 * where k is the number of separator occurrences.
 */
#ifdef __GNUC__
__attribute__((const))
#endif
std::vector<std::string> split(const std::string &s, const std::string &separators) {
    if (separators.empty())
        return std::vector<std::string>(1, s);

    std::vector<bool> isSeparator(256);
    for (size_t i = 0; i < separators.size(); i++)
        isSeparator[(unsigned char) (separators[i])] = true;

    std::vector<std::string> result;
    std::string item;
    for (size_t i = 0; i < s.length(); i++)
        if (isSeparator[(unsigned char) (s[i])]) {
            result.push_back(item);
            item = "";
        } else
            item += s[i];
    result.push_back(item);
    return result;
}

/**
 * Splits string s by character separator returning non-empty items.
 */
#ifdef __GNUC__
__attribute__((const))
#endif
std::vector<std::string> tokenize(const std::string &s, char separator) {
    std::vector<std::string> result;
    std::string item;
    for (size_t i = 0; i < s.length(); i++)
        if (s[i] == separator) {
            if (!item.empty())
                result.push_back(item);
            item = "";
        } else
            item += s[i];
    if (!item.empty())
        result.push_back(item);
    return result;
}

/**
 * Splits string s by character separators returning non-empty items.
 */
#ifdef __GNUC__
__attribute__((const))
#endif
std::vector<std::string> tokenize(const std::string &s, const std::string &separators) {
    if (separators.empty())
        return std::vector<std::string>(1, s);

    std::vector<bool> isSeparator(256);
    for (size_t i = 0; i < separators.size(); i++)
        isSeparator[(unsigned char) (separators[i])] = true;

    std::vector<std::string> result;
    std::string item;
    for (size_t i = 0; i < s.length(); i++)
        if (isSeparator[(unsigned char) (s[i])]) {
            if (!item.empty())
                result.push_back(item);
            item = "";
        } else
            item += s[i];

    if (!item.empty())
        result.push_back(item);

    return result;
}

NORETURN void __testlib_expectedButFound(TResult result, std::string expected, std::string found, const char *prepend) {
    std::string message;
    if (strlen(prepend) != 0)
        message = format("%s: expected '%s', but found '%s'",
                         compress(prepend).c_str(), compress(expected).c_str(), compress(found).c_str());
    else
        message = format("expected '%s', but found '%s'",
                         compress(expected).c_str(), compress(found).c_str());
    quit(result, message);
}

NORETURN void __testlib_expectedButFound(TResult result, double expected, double found, const char *prepend) {
    std::string expectedString = removeDoubleTrailingZeroes(format("%.12f", expected));
    std::string foundString = removeDoubleTrailingZeroes(format("%.12f", found));
    __testlib_expectedButFound(result, expectedString, foundString, prepend);
}

template<typename T>
#ifdef __GNUC__
__attribute__ ((format (printf, 4, 5)))
#endif
NORETURN void expectedButFound(TResult result, T expected, T found, const char *prependFormat = "", ...) {
    FMT_TO_RESULT(prependFormat, prependFormat, prepend);
    std::string expectedString = vtos(expected);
    std::string foundString = vtos(found);
    __testlib_expectedButFound(result, expectedString, foundString, prepend.c_str());
}

template<>
#ifdef __GNUC__
__attribute__ ((format (printf, 4, 5)))
#endif
NORETURN void
expectedButFound<std::string>(TResult result, std::string expected, std::string found, const char *prependFormat, ...) {
    FMT_TO_RESULT(prependFormat, prependFormat, prepend);
    __testlib_expectedButFound(result, expected, found, prepend.c_str());
}

template<>
#ifdef __GNUC__
__attribute__ ((format (printf, 4, 5)))
#endif
NORETURN void expectedButFound<double>(TResult result, double expected, double found, const char *prependFormat, ...) {
    FMT_TO_RESULT(prependFormat, prependFormat, prepend);
    std::string expectedString = removeDoubleTrailingZeroes(format("%.12f", expected));
    std::string foundString = removeDoubleTrailingZeroes(format("%.12f", found));
    __testlib_expectedButFound(result, expectedString, foundString, prepend.c_str());
}

template<>
#ifdef __GNUC__
__attribute__ ((format (printf, 4, 5)))
#endif
NORETURN void
expectedButFound<const char *>(TResult result, const char *expected, const char *found, const char *prependFormat,
                               ...) {
    FMT_TO_RESULT(prependFormat, prependFormat, prepend);
    __testlib_expectedButFound(result, std::string(expected), std::string(found), prepend.c_str());
}

template<>
#ifdef __GNUC__
__attribute__ ((format (printf, 4, 5)))
#endif
NORETURN void expectedButFound<float>(TResult result, float expected, float found, const char *prependFormat, ...) {
    FMT_TO_RESULT(prependFormat, prependFormat, prepend);
    __testlib_expectedButFound(result, double(expected), double(found), prepend.c_str());
}

template<>
#ifdef __GNUC__
__attribute__ ((format (printf, 4, 5)))
#endif
NORETURN void
expectedButFound<long double>(TResult result, long double expected, long double found, const char *prependFormat, ...) {
    FMT_TO_RESULT(prependFormat, prependFormat, prepend);
    __testlib_expectedButFound(result, double(expected), double(found), prepend.c_str());
}

#if __cplusplus > 199711L || defined(_MSC_VER)
template<typename T>
struct is_iterable {
    template<typename U>
    static char test(typename U::iterator *x);

    template<typename U>
    static long test(U *x);

    static const bool value = sizeof(test<T>(0)) == 1;
};

template<bool B, class T = void>
struct __testlib_enable_if {
};

template<class T>
struct __testlib_enable_if<true, T> {
    typedef T type;
};

template<typename T>
typename __testlib_enable_if<!is_iterable<T>::value, void>::type __testlib_print_one(const T &t) {
    std::cout << t;
}

template<typename T>
typename __testlib_enable_if<is_iterable<T>::value, void>::type __testlib_print_one(const T &t) {
    bool first = true;
    for (typename T::const_iterator i = t.begin(); i != t.end(); i++) {
        if (first)
            first = false;
        else
            std::cout << " ";
        std::cout << *i;
    }
}

template<>
typename __testlib_enable_if<is_iterable<std::string>::value, void>::type
__testlib_print_one<std::string>(const std::string &t) {
    std::cout << t;
}

template<typename A, typename B>
void __println_range(A begin, B end) {
    bool first = true;
    for (B i = B(begin); i != end; i++) {
        if (first)
            first = false;
        else
            std::cout << " ";
        __testlib_print_one(*i);
    }
    std::cout << std::endl;
}

template<class T, class Enable = void>
struct is_iterator {
    static T makeT();

    typedef void *twoptrs[2];

    static twoptrs &test(...);

    template<class R>
    static typename R::iterator_category *test(R);

    template<class R>
    static void *test(R *);

    static const bool value = sizeof(test(makeT())) == sizeof(void *);
};

template<class T>
struct is_iterator<T, typename __testlib_enable_if<std::is_array<T>::value>::type> {
    static const bool value = false;
};

template<typename A, typename B>
typename __testlib_enable_if<!is_iterator<B>::value, void>::type println(const A &a, const B &b) {
    __testlib_print_one(a);
    std::cout << " ";
    __testlib_print_one(b);
    std::cout << std::endl;
}

template<typename A, typename B>
typename __testlib_enable_if<is_iterator<B>::value, void>::type println(const A &a, const B &b) {
    __println_range(a, b);
}

template<typename A>
void println(const A *a, const A *b) {
    __println_range(a, b);
}

template<>
void println<char>(const char *a, const char *b) {
    __testlib_print_one(a);
    std::cout << " ";
    __testlib_print_one(b);
    std::cout << std::endl;
}

template<typename T>
void println(const T &x) {
    __testlib_print_one(x);
    std::cout << std::endl;
}

template<typename A, typename B, typename C>
void println(const A &a, const B &b, const C &c) {
    __testlib_print_one(a);
    std::cout << " ";
    __testlib_print_one(b);
    std::cout << " ";
    __testlib_print_one(c);
    std::cout << std::endl;
}

template<typename A, typename B, typename C, typename D>
void println(const A &a, const B &b, const C &c, const D &d) {
    __testlib_print_one(a);
    std::cout << " ";
    __testlib_print_one(b);
    std::cout << " ";
    __testlib_print_one(c);
    std::cout << " ";
    __testlib_print_one(d);
    std::cout << std::endl;
}

template<typename A, typename B, typename C, typename D, typename E>
void println(const A &a, const B &b, const C &c, const D &d, const E &e) {
    __testlib_print_one(a);
    std::cout << " ";
    __testlib_print_one(b);
    std::cout << " ";
    __testlib_print_one(c);
    std::cout << " ";
    __testlib_print_one(d);
    std::cout << " ";
    __testlib_print_one(e);
    std::cout << std::endl;
}

template<typename A, typename B, typename C, typename D, typename E, typename F>
void println(const A &a, const B &b, const C &c, const D &d, const E &e, const F &f) {
    __testlib_print_one(a);
    std::cout << " ";
    __testlib_print_one(b);
    std::cout << " ";
    __testlib_print_one(c);
    std::cout << " ";
    __testlib_print_one(d);
    std::cout << " ";
    __testlib_print_one(e);
    std::cout << " ";
    __testlib_print_one(f);
    std::cout << std::endl;
}

template<typename A, typename B, typename C, typename D, typename E, typename F, typename G>
void println(const A &a, const B &b, const C &c, const D &d, const E &e, const F &f, const G &g) {
    __testlib_print_one(a);
    std::cout << " ";
    __testlib_print_one(b);
    std::cout << " ";
    __testlib_print_one(c);
    std::cout << " ";
    __testlib_print_one(d);
    std::cout << " ";
    __testlib_print_one(e);
    std::cout << " ";
    __testlib_print_one(f);
    std::cout << " ";
    __testlib_print_one(g);
    std::cout << std::endl;
}

/* opts */

/**
 * A struct for a singular testlib opt, containing the raw string value,
 * and a boolean value for marking whether the opt is used.
 */
struct TestlibOpt {
    std::string value;
    bool used;

    TestlibOpt() : value(), used(false) {}
};

/**
 * Get the type of opt based on the number of `-` at the beginning and the
 * _validity_ of the key name.
 * 
 * A valid key name must start with an alphabetical character.
 * 
 * Returns: 1 if s has one `-` at the beginning, that is, "-keyName".
 *          2 if s has two `-` at the beginning, that is, "--keyName".
 *          0 otherwise. That is, if s has no `-` at the beginning, or has more
 *          than 2 at the beginning ("---keyName", "----keyName", ...), or the
 *          keyName is invalid (the first character is not an alphabetical
 *          character).
 */
size_t getOptType(char *s) {
    if (!s || strlen(s) <= 1)
        return 0;

    if (s[0] == '-') {
        if (isalpha(s[1]))
            return 1;
        else if (s[1] == '-')
            return isalpha(s[2]) ? 2 : 0;
    }

    return 0;
}

/**
 * Parse the opt at a given index, and put it into the opts maps.
 * 
 * An opt can has the following form:
 * 1) -keyName=value or --keyName=value     (ex. -n=10 --test-count=20)
 * 2) -keyName value or --keyName value     (ex. -n 10 --test-count 20)
 * 3) -kNumval       or --kNumval           (ex. -n10  --t20)
 * 4) -boolProperty  or --boolProperty      (ex. -sorted --tree-only)
 * 
 * Only the second form consumes 2 arguments. The other consumes only 1
 * argument.
 * 
 * In the third form, the key is a single character, and after the key is the
 * value. The value _should_ be a number.
 * 
 * In the forth form, the value is true.
 * 
 * Params:
 * - argc and argv: the number of command line arguments and the command line
 *   arguments themselves.
 * - index: the starting index of the opts.
 * - opts: the map containing the resulting opt.
 *  
 * Returns: the number of consumed arguments to parse the opt.
 *          0 if there is no arguments to parse.
 * 
 * Algorithm details:
 * TODO. Please refer to the implementation to see how the code handles the 3rd and 4th forms separately.
 */
size_t parseOpt(size_t argc, char *argv[], size_t index, std::map<std::string, TestlibOpt> &opts) {
    if (index >= argc)
        return 0;

    size_t type = getOptType(argv[index]), inc = 1;
    if (type > 0) {
        std::string key(argv[index] + type), val;
        size_t sep = key.find('=');
        if (sep != std::string::npos) {
            val = key.substr(sep + 1);
            key = key.substr(0, sep);
        } else {
            if (index + 1 < argc && getOptType(argv[index + 1]) == 0) {
                val = argv[index + 1];
                inc = 2;
            } else {
                if (key.length() > 1 && isdigit(key[1])) {
                    val = key.substr(1);
                    key = key.substr(0, 1);
                } else {
                    val = "true";
                }
            }
        }
        opts[key].value = val;
    } else {
        return inc;
    }

    return inc;
}

/**
 * Global list containing all the arguments in the order given in the command line.
 */
std::vector<std::string> __testlib_argv;

/**
 * Global dictionary containing all the parsed opts.
 */
std::map<std::string, TestlibOpt> __testlib_opts;

/**
 * Whether automatic no unused opts ensurement should be done. This flag will
 * be turned on when `has_opt` or `opt(key, default_value)` is called.
 * 
 * The automatic ensurement can be suppressed when
 * __testlib_ensureNoUnusedOptsSuppressed is true.
 */
bool __testlib_ensureNoUnusedOptsFlag = false;

/**
 * Suppress no unused opts automatic ensurement. Can be set to true with
 * `suppressEnsureNoUnusedOpts()`.
 */
bool __testlib_ensureNoUnusedOptsSuppressed = false;

/**
 * Parse command line arguments into opts.
 * The results are stored into __testlib_argv and __testlib_opts.
 */
void prepareOpts(int argc, char *argv[]) {
    if (argc <= 0)
        __testlib_fail("Opts: expected argc>=0 but found " + toString(argc));
    size_t n = static_cast<size_t>(argc); // NOLINT(hicpp-use-auto,modernize-use-auto)
    __testlib_opts = std::map<std::string, TestlibOpt>();
    for (size_t index = 1; index < n; index += parseOpt(n, argv, index, __testlib_opts));
    __testlib_argv = std::vector<std::string>(n);
    for (size_t index = 0; index < n; index++)
        __testlib_argv[index] = argv[index];
}

/**
 * An utility function to get the argument with a given index. This function
 * also print a readable message when no arguments are found.
 */
std::string __testlib_indexToArgv(int index) {
    if (index < 0 || index >= int(__testlib_argv.size()))
        __testlib_fail("Opts: index '" + toString(index) + "' is out of range [0,"
            + toString(__testlib_argv.size()) + ")");
    return __testlib_argv[size_t(index)];
}

/**
 * An utility function to get the opt with a given key . This function
 * also print a readable message when no opts are found.
 */
std::string __testlib_keyToOpts(const std::string &key) {
    auto it = __testlib_opts.find(key);
    if (it == __testlib_opts.end())
        __testlib_fail("Opts: unknown key '" + compress(key) + "'");
    it->second.used = true;
    return it->second.value;
}

template<typename T>
T optValueToIntegral(const std::string &s, bool nonnegative);

long double optValueToLongDouble(const std::string &s);

std::string parseExponentialOptValue(const std::string &s) {
    size_t pos = std::string::npos;
    for (size_t i = 0; i < s.length(); i++)
        if (s[i] == 'e' || s[i] == 'E') {
            if (pos != std::string::npos)
                __testlib_fail("Opts: expected typical exponential notation but '" + compress(s) + "' found");
            pos = i;
        }
    if (pos == std::string::npos)
        return s;
    std::string e = s.substr(pos + 1);
    if (!e.empty() && e[0] == '+')
        e = e.substr(1);
    if (e.empty())
        __testlib_fail("Opts: expected typical exponential notation but '" + compress(s) + "' found");
    if (e.length() > 20)
        __testlib_fail("Opts: expected typical exponential notation but '" + compress(s) + "' found");
    int ne = optValueToIntegral<int>(e, false);
    std::string num = s.substr(0, pos);
    if (num.length() > 20)
        __testlib_fail("Opts: expected typical exponential notation but '" + compress(s) + "' found");
    if (!num.empty() && num[0] == '+')
        num = num.substr(1);
    optValueToLongDouble(num);
    bool minus = false;
    if (num[0] == '-') {
        minus = true;
        num = num.substr(1);
    }
    for (int i = 0; i < +ne; i++) {
        size_t sep = num.find('.');
        if (sep == std::string::npos)
            num += '0';
        else {
            if (sep + 1 == num.length())
                num[sep] = '0';
            else
                std::swap(num[sep], num[sep + 1]);
        }
    }
    for (int i = 0; i < -ne; i++) {
        size_t sep = num.find('.');
        if (sep == std::string::npos)
            num.insert(num.begin() + int(num.length()) - 1, '.');
        else {
            if (sep == 0)
                num.insert(num.begin() + 1, '0');
            else
                std::swap(num[sep - 1], num[sep]);
        }
    }
    while (!num.empty() && num[0] == '0')
        num = num.substr(1);
    while (num.find('.') != std::string::npos && num.back() == '0')
        num = num.substr(0, num.length() - 1);
    if (!num.empty() && num.back() == '.')
        num = num.substr(0, num.length() - 1);
    if ((!num.empty() && num[0] == '.') || num.empty())
        num.insert(num.begin(), '0');
    return (minus ? "-" : "") + num;
}

template<typename T>
T optValueToIntegral(const std::string &s_, bool nonnegative) {
    std::string s(parseExponentialOptValue(s_));
    if (s.empty())
        __testlib_fail("Opts: expected integer but '" + compress(s_) + "' found");
    T value = 0;
    long double about = 0.0;
    signed char sign = +1;
    size_t pos = 0;
    if (s[pos] == '-') {
        if (nonnegative)
            __testlib_fail("Opts: expected non-negative integer but '" + compress(s_) + "' found");
        sign = -1;
        pos++;
    }
    for (size_t i = pos; i < s.length(); i++) {
        if (s[i] < '0' || s[i] > '9')
            __testlib_fail("Opts: expected integer but '" + compress(s_) + "' found");
        value = T(value * 10 + s[i] - '0');
        about = about * 10 + s[i] - '0';
    }
    value *= sign;
    about *= sign;
    if (fabsl(value - about) > 0.1)
        __testlib_fail("Opts: integer overflow: expected integer but '" + compress(s_) + "' found");
    return value;
}

long double optValueToLongDouble(const std::string &s_) {
    std::string s(parseExponentialOptValue(s_));
    if (s.empty())
        __testlib_fail("Opts: expected float number but '" + compress(s_) + "' found");
    long double value = 0.0;
    signed char sign = +1;
    size_t pos = 0;
    if (s[pos] == '-') {
        sign = -1;
        pos++;
    }
    bool period = false;
    long double mul = 1.0;
    for (size_t i = pos; i < s.length(); i++) {
        if (s[i] == '.') {
            if (period)
                __testlib_fail("Opts: expected float number but '" + compress(s_) + "' found");
            else {
                period = true;
                continue;
            }
        }
        if (period)
            mul *= 10.0;
        if (s[i] < '0' || s[i] > '9')
            __testlib_fail("Opts: expected float number but '" + compress(s_) + "' found");
        if (period)
            value += (s[i] - '0') / mul;
        else
            value = value * 10 + s[i] - '0';
    }
    value *= sign;
    return value;
}

/**
 * Return true if there is an opt with a given key.
 * 
 * By calling this function, automatic ensurement for no unused opts will be
 * done when the program is finalized. Call suppressEnsureNoUnusedOpts() to
 * turn it off.
 */
bool has_opt(const std::string &key) {
    __testlib_ensureNoUnusedOptsFlag = true;
    return __testlib_opts.count(key) != 0;
}

/* About the following part for opt with 2 and 3 arguments.
 * 
 * To parse the argv/opts correctly for a give type (integer, floating point or
 * string), some meta programming must be done to determine the type of
 * the type, and use the correct parsing function accordingly.
 * 
 * The pseudo algorithm for determining the type of T and parse it accordingly
 * is as follows:
 * 
 * if (T is integral type) {
 *   if (T is unsigned) {
 *     parse the argv/opt as an **unsigned integer** of type T.
 *   } else {
 *     parse the argv/opt as an **signed integer** of type T.
 * } else {
 *   if (T is floating point type) {
 *     parse the argv/opt as an **floating point** of type T.
 *   } else {
 *     // T should be std::string
 *     just the raw content of the argv/opts.
 *   }
 * }
 * 
 * To help with meta programming, some `opt` function with 2 or 3 arguments are
 * defined.
 * 
 * Opt with 3 arguments:    T opt(true/false is_integral, true/false is_unsigned, index/key)
 * 
 *   + The first argument is for determining whether the type T is an integral
 *   type. That is, the result of std::is_integral<T>() should be passed to
 *   this argument. When false, the type _should_ be either floating point or a
 *   std::string.
 *   
 *   + The second argument is for determining whether the signedness of the type
 *   T (if it is unsigned or signed). That is, the result of
 *   std::is_unsigned<T>() should be passed to this argument. This argument can
 *   be ignored if the first one is false, because it only applies to integer.
 *
 * Opt with 2 arguments:    T opt(true/false is_floating_point, index/key)
 *   + The first argument is for determining whether the type T is a floating
 *   point type. That is, the result of std::is_floating_point<T>() should be
 *   passed to this argument. When false, the type _should_ be a std::string.
 */

template<typename T>
T opt(std::false_type is_floating_point, int index);

template<>
std::string opt(std::false_type /*is_floating_point*/, int index) {
    return __testlib_indexToArgv(index);
}

template<typename T>
T opt(std::true_type /*is_floating_point*/, int index) {
    return T(optValueToLongDouble(__testlib_indexToArgv(index)));
}

template<typename T, typename U>
T opt(std::false_type /*is_integral*/, U /*is_unsigned*/, int index) {
    return opt<T>(std::is_floating_point<T>(), index);
}

template<typename T>
T opt(std::true_type /*is_integral*/, std::false_type /*is_unsigned*/, int index) {
    return optValueToIntegral<T>(__testlib_indexToArgv(index), false);
}

template<typename T>
T opt(std::true_type /*is_integral*/, std::true_type /*is_unsigned*/, int index) {
    return optValueToIntegral<T>(__testlib_indexToArgv(index), true);
}

template<>
bool opt(std::true_type /*is_integral*/, std::true_type /*is_unsigned*/, int index) {
    std::string value = __testlib_indexToArgv(index);
    if (value == "true" || value == "1")
        return true;
    if (value == "false" || value == "0")
        return false;
    __testlib_fail("Opts: opt by index '" + toString(index) + "': expected bool true/false or 0/1 but '"
            + compress(value) + "' found");
}

/**
 * Return the parsed argv by a given index.
 */
template<typename T>
T opt(int index) {
    return opt<T>(std::is_integral<T>(), std::is_unsigned<T>(), index);
}

/**
 * Return the raw string value of an argv by a given index.
 */
std::string opt(int index) {
    return opt<std::string>(index);
}

/**
 * Return the parsed argv by a given index. If the index is bigger than
 * the number of argv, return the given default_value.
 */
template<typename T>
T opt(int index, const T &default_value) {
    if (index >= int(__testlib_argv.size())) {
        return default_value;
    }
    return opt<T>(index);
}

/**
 * Return the raw string value of an argv by a given index. If the index is
 * bigger than the number of argv, return the given default_value.
 */
std::string opt(int index, const std::string &default_value) {
    return opt<std::string>(index, default_value);
}

template<typename T>
T opt(std::false_type is_floating_point, const std::string &key);

template<>
std::string opt(std::false_type /*is_floating_point*/, const std::string &key) {
    return __testlib_keyToOpts(key);
}

template<typename T>
T opt(std::true_type /*is_integral*/, const std::string &key) {
    return T(optValueToLongDouble(__testlib_keyToOpts(key)));
}

template<typename T, typename U>
T opt(std::false_type /*is_integral*/, U, const std::string &key) {
    return opt<T>(std::is_floating_point<T>(), key);
}

template<typename T>
T opt(std::true_type /*is_integral*/, std::false_type /*is_unsigned*/, const std::string &key) {
    return optValueToIntegral<T>(__testlib_keyToOpts(key), false);
}

template<typename T>
T opt(std::true_type /*is_integral*/, std::true_type /*is_unsigned*/, const std::string &key) {
    return optValueToIntegral<T>(__testlib_keyToOpts(key), true);
}

template<>
bool opt(std::true_type /*is_integral*/, std::true_type /*is_unsigned*/, const std::string &key) {
    if (!has_opt(key))
        return false;
    std::string value = __testlib_keyToOpts(key);
    if (value == "true" || value == "1")
        return true;
    if (value == "false" || value == "0")
        return false;
    __testlib_fail("Opts: key '" + compress(key) + "': expected bool true/false or 0/1 but '"
        + compress(value) + "' found");
}

/**
 * Return the parsed opt by a given key.
 */
template<typename T>
T opt(const std::string &key) {
    return opt<T>(std::is_integral<T>(), std::is_unsigned<T>(), key);
}

/**
 * Return the raw string value of an opt by a given key
 */
std::string opt(const std::string &key) {
    return opt<std::string>(key);
}

/* Scorer started. */

enum TestResultVerdict {
    SKIPPED,
    OK,
    WRONG_ANSWER,
    RUNTIME_ERROR,
    TIME_LIMIT_EXCEEDED,
    IDLENESS_LIMIT_EXCEEDED,
    MEMORY_LIMIT_EXCEEDED,
    COMPILATION_ERROR,
    CRASHED,
    FAILED
};

std::string serializeVerdict(TestResultVerdict verdict) {
    switch (verdict) {
        case SKIPPED: return "SKIPPED";
        case OK: return "OK";
        case WRONG_ANSWER: return "WRONG_ANSWER";
        case RUNTIME_ERROR: return "RUNTIME_ERROR";
        case TIME_LIMIT_EXCEEDED: return "TIME_LIMIT_EXCEEDED";
        case IDLENESS_LIMIT_EXCEEDED: return "IDLENESS_LIMIT_EXCEEDED";
        case MEMORY_LIMIT_EXCEEDED: return "MEMORY_LIMIT_EXCEEDED";
        case COMPILATION_ERROR: return "COMPILATION_ERROR";
        case CRASHED: return "CRASHED";
        case FAILED: return "FAILED";
    }
    throw "Unexpected verdict";
}

TestResultVerdict deserializeTestResultVerdict(std::string s) {
    if (s == "SKIPPED")
        return SKIPPED;
    else if (s == "OK")
        return OK;
    else if (s == "WRONG_ANSWER")
        return WRONG_ANSWER;
    else if (s == "RUNTIME_ERROR")
        return RUNTIME_ERROR;
    else if (s == "TIME_LIMIT_EXCEEDED")
        return TIME_LIMIT_EXCEEDED;
    else if (s == "IDLENESS_LIMIT_EXCEEDED")
        return IDLENESS_LIMIT_EXCEEDED;
    else if (s == "MEMORY_LIMIT_EXCEEDED")
        return MEMORY_LIMIT_EXCEEDED;
    else if (s == "COMPILATION_ERROR")
        return COMPILATION_ERROR;
    else if (s == "CRASHED")
        return CRASHED;
    else if (s == "FAILED")
        return FAILED;
    ensuref(false, "Unexpected serialized TestResultVerdict");
    // No return actually.
    return FAILED;
}

struct TestResult {
    int testIndex;
    std::string testset;
    std::string group;
    TestResultVerdict verdict;
    double points;
    long long timeConsumed;
    long long memoryConsumed;
    std::string input;
    std::string output;
    std::string answer;
    int exitCode;
    std::string checkerComment;
};

std::string serializePoints(double points) {
    if (std::isnan(points))
        return "";
    else {
        char c[64];
        snprintf(c, 64, "%.03lf", points);
        return c;
    }
}

double deserializePoints(std::string s) {
    if (s.empty())
        return std::numeric_limits<double>::quiet_NaN();
    else {
        double result;
        ensuref(sscanf(s.c_str(), "%lf", &result) == 1, "Invalid serialized points");
        return result;
    }                                              
}

std::string escapeTestResultString(std::string s) {
    std::string result;
    for (size_t i = 0; i < s.length(); i++) {
        if (s[i] == '\r')
            continue;
        if (s[i] == '\n') {
            result += "\\n";
            continue;
        }
        if (s[i] == '\\' || s[i] == ';')
            result += '\\';
        result += s[i];
    }
    return result;
}

std::string unescapeTestResultString(std::string s) {
    std::string result;
    for (size_t i = 0; i < s.length(); i++) {
        if (s[i] == '\\' && i + 1 < s.length()) {
            if (s[i + 1] == 'n') {
                result += '\n';
                i++;
                continue;
            } else if (s[i + 1] == ';' || s[i + 1] == '\\') {
                result += s[i + 1];
                i++;
                continue;
            }
        }
        result += s[i];
    }
    return result;
}

std::string serializeTestResult(TestResult tr) {
    std::string result;
    result += std::to_string(tr.testIndex);
    result += ";";
    result += escapeTestResultString(tr.testset);
    result += ";";
    result += escapeTestResultString(tr.group);
    result += ";";
    result += serializeVerdict(tr.verdict);
    result += ";";
    result += serializePoints(tr.points);
    result += ";";
    result += std::to_string(tr.timeConsumed);
    result += ";";
    result += std::to_string(tr.memoryConsumed);
    result += ";";
    result += escapeTestResultString(tr.input);
    result += ";";
    result += escapeTestResultString(tr.output);
    result += ";";
    result += escapeTestResultString(tr.answer);
    result += ";";
    result += std::to_string(tr.exitCode);
    result += ";";
    result += escapeTestResultString(tr.checkerComment);
    return result;
}

TestResult deserializeTestResult(std::string s) {
    std::vector<std::string> items;
    std::string t;
    for (size_t i = 0; i < s.length(); i++) {
        if (s[i] == '\\') {
            t += s[i];
            if (i + 1 < s.length())
                t += s[i + 1];
            i++;
            continue;
        } else {
            if (s[i] == ';') {
                items.push_back(t);
                t = "";
            } else
                t += s[i];
        }
    }
    items.push_back(t);

    ensuref(items.size() == 12, "Invalid TestResult serialization: expected exactly 12 items");
    
    TestResult tr;
    size_t pos = 0;
    tr.testIndex = stoi(items[pos++]);
    tr.testset = unescapeTestResultString(items[pos++]);
    tr.group = unescapeTestResultString(items[pos++]);
    tr.verdict = deserializeTestResultVerdict(items[pos++]);
    tr.points = deserializePoints(items[pos++]);
    tr.timeConsumed = stoll(items[pos++]);
    tr.memoryConsumed = stoll(items[pos++]);
    tr.input = unescapeTestResultString(items[pos++]);
    tr.output = unescapeTestResultString(items[pos++]);
    tr.answer = unescapeTestResultString(items[pos++]);
    tr.exitCode = stoi(items[pos++]);
    tr.checkerComment = unescapeTestResultString(items[pos++]);
    
    return tr;
}

std::vector<TestResult> readTestResults(std::string fileName) {
    std::ifstream stream;
    stream.open(fileName.c_str(), std::ios::in);
    ensuref(stream.is_open(), "Can't read test results file '%s'", fileName.c_str());
    std::vector<TestResult> result;
    std::string line;
    while (getline(stream, line))
        if (!line.empty())
            result.push_back(deserializeTestResult(line));
    stream.close();
    return result;
}

std::function<double(std::vector<TestResult>)> __testlib_scorer;

struct TestlibScorerGuard {
    ~TestlibScorerGuard() {
        if (testlibMode == _scorer) {
            std::vector<TestResult> testResults;
            while (!inf.eof()) {
                std::string line = inf.readLine();
                if (!line.empty())
                    testResults.push_back(deserializeTestResult(line));
            }
            inf.readEof();
            printf("%.3f\n", __testlib_scorer(testResults));
        }
    }
} __testlib_scorer_guard;

void registerScorer(int argc, char *argv[], std::function<double(std::vector<TestResult>)> scorer) {
    /* Suppress unused. */
    (void)(argc), (void)(argv);

    __testlib_ensuresPreconditions();

    testlibMode = _scorer;
    __testlib_set_binary(stdin);

    inf.init(stdin, _input);
    inf.strict = false;

    __testlib_scorer = scorer;
}

/* Scorer ended. */

/**
 * Return the parsed opt by a given key. If no opts with the given key are
 * found, return the given default_value.
 * 
 * By calling this function, automatic ensurement for no unused opts will be
 * done when the program is finalized. Call suppressEnsureNoUnusedOpts() to
 * turn it off.
 */
template<typename T>
T opt(const std::string &key, const T &default_value) {
    if (!has_opt(key)) {
        return default_value;
    }
    return opt<T>(key);
}

/**
 * Return the raw string value of an opt by a given key. If no opts with the
 * given key are found, return the given default_value.
 * 
 * By calling this function, automatic ensurement for no unused opts will be
 * done when the program is finalized. Call suppressEnsureNoUnusedOpts() to
 * turn it off.
 */
std::string opt(const std::string &key, const std::string &default_value) {
    return opt<std::string>(key, default_value);
}

/**
 * Check if all opts are used. If not, __testlib_fail is called.
 * Should be used after calling all opt() function calls.
 * 
 * This function is useful when opt() with default_value for checking typos
 * in the opt's key.
 */
void ensureNoUnusedOpts() {
    for (const auto &opt: __testlib_opts) {
        if (!opt.second.used) {
            __testlib_fail(format("Opts: unused key '%s'", compress(opt.first).c_str()));
        }
    }
}

void suppressEnsureNoUnusedOpts() {
    __testlib_ensureNoUnusedOptsSuppressed = true;
}

void TestlibFinalizeGuard::autoEnsureNoUnusedOpts() {
    if (__testlib_ensureNoUnusedOptsFlag && !__testlib_ensureNoUnusedOptsSuppressed) {
        ensureNoUnusedOpts();
    }
}

TestlibFinalizeGuard testlibFinalizeGuard;

#endif
#endif
