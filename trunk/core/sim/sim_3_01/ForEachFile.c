/*	This file is part of the auxiliaries library.
	Written by Dick Grune, Vrije Universiteit, Amsterdam.
	$Id: ForEachFile.c,v 1.23 2016-02-22 08:20:42 Gebruiker Exp $
*/

#include	<string.h>
#include	<sys/types.h>
#include	<sys/stat.h>
#include	<dirent.h>
#include	<errno.h>

#include	"ForEachFile.h"

/* Library module source prelude */
#undef	_FOREACHFILE_CODE_
#ifndef	lint
#define	_FOREACHFILE_CODE_
#endif
#ifdef	LIB
#define	_FOREACHFILE_CODE_
#endif

#ifdef	_FOREACHFILE_CODE_

/* Library module source code */

							/* TREE SCANNING */
#ifdef	S_IFLNK				/* system with symbolic links */
#define	LSTAT	lstat
#else	/* S_IFLNK */
#define	LSTAT	Stat
#endif	/* S_IFLNK */

int
is_dirstat(const struct stat *fs) {
	if (!fs) return 0;
	return ((fs->st_mode & S_IFMT) == S_IFDIR);
}

int
is_Dirname(const Fchar *Fn) {
	if (!Fn) return 0;
	struct stat stb;
	if (LSTAT(Fn, &stb) < 0) return 0;
	return ((stb.st_mode & S_IFMT) == S_IFDIR);
}

int
is_Admin_Dirname(const Fchar *Fn) {
	if (!Fn) return 0;
	return	Fnamecmp(Fn, str2Fname(".")) == 0
	||	Fnamecmp(Fn, str2Fname("..")) == 0;
}

static void do_dir(
	Fchar *Fn,
	int (*proc)(const Fchar *, const char *, const struct stat *)
);

static void
do_name(Fchar *Fn,
	int (*proc)(const Fchar *, const char *, const struct stat *),
	int top_level
) {
	/* examine Fn */
	struct stat fs;
	if (LSTAT(Fn, &fs) < 0) {
		(void)(*proc)(Fn, strerror(errno), 0);
		return;
	}

	/* report on Fn and get possible return code */
	int rc = (*proc)(Fn, (char*)0, &fs);

	if (!is_dirstat(&fs)) return;

	/* Fn is a directory, so rc may be meaningful */
	if (!top_level) if (!rc) return;

#ifdef	S_IFLNK
	/* don't follow links */
	if ((fs.st_mode & S_IFMT) == S_IFLNK) return;
#endif

	do_dir(Fn, proc);
}

static void
do_dir(
	Fchar *Fn,
	int (*proc)(const Fchar *, const char *, const struct stat *)
) {

	/* treat directory */
	Dir_t *dir = Opendir(Fn);
	if (dir == 0) {
		(void)(*proc)(Fn, "directory not readable", 0);
		return;
	}

	/* scan new directory */

	/* append separator */
	int Fn_len = Fnamelen(Fn);
	Fn[Fn_len++] = '/';
	Fn[Fn_len] = '\0';

	/* descend */
	Dirent_t *dent;
	while ((dent = Readdir(dir)) != (Dirent_t *)0) {
		const Fchar *d_name = dent->d_name;
		if (is_Admin_Dirname(d_name)) continue;

		/* append name */
		Fnamecat(Fn, d_name);
		do_name(Fn, proc, 0);
		/* remove appended name*/
		Fn[Fn_len] = '\0';
	}
	/* remove appended separator*/
	Fn[--Fn_len] = '\0';
	Closedir(dir);
}

static Fchar MSDOS_sep = (Fchar)'\\';
static Fchar UNIX_sep = (Fchar)'/';

static void
clean_name(Fchar *Fn) {
	/* remove a trailing separator */
	int Fn_len = Fnamelen(Fn);
	if (Fn_len > 1 && (Fn[Fn_len-1] == MSDOS_sep || Fn[Fn_len-1] == UNIX_sep)) {
		Fn[Fn_len-1] = '\0';
	}
}

							/* THE ENTRIES */
void
ForEachFile(
	const Fchar *Fname,
	int (*proc)(const Fchar *, const char *, const struct stat *)
) {
	if (!Fname || !Fname[0] || !proc) return;	/* just to make sure */

	/* get Fn */
	Fchar Fn[MAX_FILE_NAME_LENGTH];
	Fnamecpy(Fn, Fname);
	clean_name(Fn);

	/* top level */
	do_name(Fn, proc, 1);
}

/* End library module source code */
#endif	/* _FOREACHFILE_CODE_ */

#ifdef	lint
static void
satisfy_lint(void *x) {
	(void)is_dirstat(0);
	(void)is_Dirname(0);
	(void)is_Admin_Dirname(0);
	ForEachFile(0, 0);
	satisfy_lint(x);
}
#endif	/* lint */
