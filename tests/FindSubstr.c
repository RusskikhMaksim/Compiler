#include <stdio.h>  // Для printf
#include <string.h> // Для strstr
int substr (void)
{
   /*
   char str1 [11]="0123456789;

   char str2 [10]="345";

   char *istr;

   //Поиск строки
   istr = strstr (str1,str2);

   //Вывод результата поиска на консоль
   if ( istr == NULL)
      printf ("Row not found\n");
   else
      printf ("The search string begins with the character %d\n",istr-str1+1);

   return 0;
}