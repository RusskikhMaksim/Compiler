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

