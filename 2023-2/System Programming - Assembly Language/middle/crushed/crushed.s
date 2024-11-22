.text
.extern bb
crushed: .global crushed
	@ r0 = int a, r1 = int b
	@ return bb(a) + bb(b)
	push	{r4, lr}
	mov		r4, r1
	bl		bb
	mov		r1, r0
	mov		r0, r4
	mov		r4, r1
	bl		bb
	add 	r0, r0, r4
	pop		{r4, pc}
