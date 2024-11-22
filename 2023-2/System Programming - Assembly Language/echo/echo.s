.text
_start: .global _start
	@sys_read (fd, pstr, max_len)
	@r7=3	   r0  r1    r2
	mov r0, #0      @fd <- stdinput
	ldr r1, =buffer @pstr <- *buffer
	mov r2, #100    @max_len <- 100
	mov r7, #3      @syscall <- sys_read
	swi 0	        @system call

	ldrb r7, [r1]
	sub  r7, r7, #32
	strb r7, [r1]

	@sys_write (fd, pstr, len)
	@r7=4		r0	r1    r2
	mov	 r0, #1	     @fd <- stdout
	mov  r2, #100	 @len <- 100
	mov r7, #4	     @syscall <- sys_write
	swi	0		     @system call

	@sys_exit(exitcode)
	@r7=1     r0
	mov	r0, #0	@exitcode <- 0
	mov r7, #1	@syscall <- sys_exit
	swi	0		@system call
.bss
buffer:
	.skip 101	@101 Byte not clear
.end
