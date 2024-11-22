.extern bb

crushed: .global crushed
	push	{r4, lr}	@r4, lr backup

	mov		r4, r1		@r1(second parameter) -> r4 escape
	bl		bb

	mov		r1, r0		@r0(first result) -> r1 temporal escape
	mov		r0, r4		@r4(second parameter) -> r0 setting
	mov		r4, r1		@r1(first result) -> r4 escape
	bl		bb

	add		r0, r0, r4	@r0(second result)
	pop		{r4, pc}	@r4 recover and return
