.text
bb: .global bb
	@ r0 = int a
	@ return a * 2
	mov	r0, r0, LSL #1
	mov r1, #1
	mov r2, #2
	mov r3, #3
	mov r12, #12
	bx	lr
