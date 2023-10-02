#include<math.h>
#include<stdarg.h>
#include<assert.h>
#include<string.h>

#include "dynArray.h"

int changeBase(int numLets, char *s, int seqLen) {
  int i, j, k;
  int retVal = 0;
  char intStr[2];
  assert(strlen(s)>=seqLen);
  
  for(i=seqLen-1; i>=0; i--) {
    intStr[0] = s[i];
    intStr[1] = '\0';
    k = atoi(intStr);
    j = k * (int)pow((double)numLets, (double)(seqLen-1-i));
    //    printf("%d**", j);
    assert(k<numLets);
    
    retVal += j;
  }

  return retVal;
}

char *changeBaseBack(int baseTen, int newBase, int seqLen) {
  int digitCount;
  int tempBaseTen;
  int i;
  int *tempArry;
  char *retStr;

  digitCount = 0;
  assert(newBase<10);
  
  tempBaseTen = baseTen;
  while(tempBaseTen) {
    int residual = tempBaseTen % newBase;
    //  printf("**%d", residual);
    tempBaseTen = tempBaseTen / newBase;
    digitCount++;
  }
  
  tempArry = (int *)calloc(digitCount, sizeof(int));
  digitCount = 0;
  tempBaseTen = baseTen;


  while(tempBaseTen) {
    int residual = tempBaseTen % newBase;
    tempBaseTen = tempBaseTen / newBase;
    tempArry[digitCount++] = residual;
    //    printf("res%d\n", residual);
  }

  retStr = (char *)calloc(digitCount+1, sizeof(char));
  for(i=0; i<digitCount; i++) {
    //    printf("here");
    //  tee-hee
    //     sprintf(&temp[0], "%d",tempArry[digitCount-i]);
    retStr[i] = tempArry[digitCount-i-1] + '0';
    //    printf("%d ==> %dff", digitCount-i, tempArry[digitCount-i-1]);
  }
  retStr[++i] = '\0';
  
  return retStr;

}
    

char *padStrWithZeros(int digCount, char *s) { 
  char *retStr;
  int i;
  int numMissingZeros;
  if(digCount > (strlen(s)-1) || strlen(s) == 0) {
    numMissingZeros = digCount - strlen(s);
    //    printf("%dmissing\n", numMissingZeros);
    assert(retStr = (char *)calloc(numMissingZeros+strlen+1, sizeof(char)));
    for(i=0; i<numMissingZeros; i++) {
      //      printf("got here");
      retStr[i] = '0';
    }
    retStr[++i] = '\0';
    strcat(retStr, s);
  } else {
    retStr = s;
  }

  return retStr;
}
     



/*
int indexOfEntry(int numLets, int seqLen,...) {
  va_list ap;
  int i;
  int index = 0;
  int offsetContrib;


  //  printf("%f", log((double)numLets));
  va_start(ap, seqLen);
  for(i=seqLen; i>0; i--) {
    int thisArgument;
    thisArgument = va_arg(ap,int);
    assert(thisArgument < numLets);
    offsetContrib = thisArgument * (int)pow((double)numLets, (double)i);
    index += offsetContrib;
    //    printf("%d", (int)pow((double)numLets, (double)i));
  }
  return index;
}
*/
