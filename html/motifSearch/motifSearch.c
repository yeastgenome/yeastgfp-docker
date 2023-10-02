#define SEQUENCE_LENGTH 10;
#define NUM_LETTERS 4;

#include<math.h>
#include "dynArray.h"

int findSeqIndexFreq(char *s, int numLets, int seqLen, unsigned char *countArray);
void printArray(int numLets, int seqLen, unsigned char *countArray);


int main(void) {
  int numLetters;
  int seqLen;
  int arraySize;
  unsigned char *countArray;
  float f;

  numLetters = NUM_LETTERS;
  seqLen = SEQUENCE_LENGTH;
  arraySize = (int)pow((double)numLetters, (double)(seqLen));
  //  printf("**%d\n", arraySize);
  countArray = (unsigned char *)calloc(arraySize, sizeof(char));
  printf("callocing %d\n", countArray);
  //  printf("%d", changeBase(2,"110", 3));
  findSeqIndexFreq("1233321323102132132313321231211323132132132", numLetters, seqLen, countArray);

  //  printArray(numLetters, seqLen, countArray);

  return 0;
}



int findSeqIndexFreq(char *s, int numLets, int seqLen, unsigned char *countArray) {
  int i;
  int wholeLen;
  char *subStr;

  subStr = (char *)malloc((seqLen +1) * sizeof(char));
  wholeLen = strlen(s);
  //  printf("\n**%d\n", wholeLen);
  for(i=0; i<=wholeLen-seqLen; i++) {
    strncpy(subStr, s+i, seqLen);
    subStr[seqLen]='\0';
    // printf("%s\n", subStr);
    //    fflush();
    countArray[changeBase(numLets, subStr, seqLen)]++;
  }

    printArray(numLets, seqLen, countArray);
  


}


void printArray(int numLets, int seqLen, unsigned char *countArray) {
  int arraySize;
  int i;
  char *myStr;

  printf("got here\n");
  arraySize = (int)pow((double)numLets, (double)(seqLen));
  for(i=0; i<arraySize; i++) {
    //myStr = padStrWithZeros(100, changeBaseBack(i, numLets, seqLen));
	//	printf("got here");
    myStr = padStrWithZeros(seqLen, changeBaseBack(i, numLets, seqLen));
    //printf("%s -> %d\n", myStr, countArray[i]);
    free(myStr);
  }



}

  
