.text
get_array_elem: .global get_array_elem
	@ r0 = char *str, r1 = int num
	ldrb	r0, [r0, r1]
	bx		lr
