#include <stdio.h>


int findMin(void) {
  int c = 4;
  int x[4] = {1, 2, 3, 4};
  int y[4] = {2, 2, 3, 4};
  int w;
   w = 5;
  int i = (10 + 5)*2 + c + w;
  int min, j;



  int index = i + 3;
  int countOfElements = 0;
  //int array[10];



  printf("Enter the number of elements in the array: ");
  scanf("%d", &countOfElements);

  //int array[countOfElements];

  printf("Enter the array: ");

  char ll = "c";
  while(ll == "c") {

    //scanf("%d", &array[i]);
    i = 10 + 1;
    }

  min = 0;

  index = 0;
  j = 5;
  while(j < 5) {
    if(j < min){
      min = j;
      index = j;
    }
    j = j + 1;
  }

  printf("The smallest array element: %d, is in position %d", min, index);

  return 0;
}