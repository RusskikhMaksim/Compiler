#include <stdio.h>

int findNod(void) {
 int firstNum, secondNum;
 int result;

 printf("input first number: ");
 scanf("%d", &firstNum);
 printf("input second number: ");
 scanf("%d", &secondNum);

result = Nod(firstNum, secondNum);
printf("result : %d", result);
  return 0;
}



int Nod(int a, int b)
{
    while (a && b)
        if (a >= b)
           a %= b;
        else
           b %= a;
    return a | b;
}

//////////////////////////////////////////////////////////////////////
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
    i++;
  }

  min = array[0];
  index = 0;
  j = 0;
  while(j < countOfElements){
    if(array[j] < min){
      min = array[j];
      index = j;
    }
    j++
  }

  printf("The smallest array element: %d, is in position %d", min, index);

  return 0;
}