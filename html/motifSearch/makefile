LDFLAGS = -lm 
CCFLAGS =  -Wmissing-prototypes -Wmissing-declarations -Wstrict-prototypes -Werror
motifSearch : motifSearch.o dynArray.o
	cc $(LDFLAGS) -o motifSearch motifSearch.o dynArray.o
motifSearch.o : motifSearch.c
	cc $(CCFLAGS) -c motifSearch.c
dynArray.o : dynArray.c
	cc $(CCFLAGS) -c dynArray.c
clean :
	rm -rf *.o motifSearch *~