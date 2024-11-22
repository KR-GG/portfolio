#include <stdio.h>

char get_array_elem(char* arr, int idx);

int main()
{
	char arr[10] = {0, 1, 2, 3, 4, 5, 6, 7, 8, 9};
	printf("%d\n", get_array_elem(arr, 5));
}
