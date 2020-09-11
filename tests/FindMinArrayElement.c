#include <stdio.h>

int findMin(void) {
  int min;
  printf("Enter the number of elements in the array: ");
  scanf("%d", &countOfElements);

  int array[countOfElements];

  printf("Enter the array: ");
  int i = 0;
  while(i < countOfElements){
    scanf("%d", &array[i]);
    i = i + 1;
  }

  min = array[0];
  index = 0;
  j = 0;
  while(j < countOfElements){
    if(array[j] < min){
      min = array[j];
      index = j;
    }
    j = j + 1;
  }

  printf("The smallest array element: %d, is in position %d", min, index);

  return 0;
}