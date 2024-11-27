all: judged.cc judged.http judgehub
	g++ -Wall -c -DOJ_USE_MYSQL  -I/www/server/mysql/include -I/usr/local/mysql/include/mysql -I/usr/include/mysql  judged.cc
	g++ -Wall -o judged judged.o -L/www/server/mysql/lib -L/usr/lib/mysql  -L/usr/lib64/mysql  -lmysqlclient

judged.http: judged.cc
	g++ -Wall -o judged.http judged.cc

judgedhub: judgehub.cc
	g++ -Wall -o judgehub judgehub.cc

