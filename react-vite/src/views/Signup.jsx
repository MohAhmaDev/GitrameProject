import React, { useRef, useState } from 'react'
import { Link } from 'react-router-dom';
import axiosClient from '../axios-client';
import { useStateContext } from '../contexts/ContextProvider';
import { useForm } from 'react-hook-form'

import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup'


export default function Signup() {

  const userSchema = yup.object().shape({
    name: yup.string().required(),
    email: yup.string().email().required(),                                             
    password: yup.string().min(8).required(),
    password_confirmation: yup.string().min(8).oneOf([yup.ref('password'), null], "Passwords don't match.").required()
  })
  

  const { register, handleSubmit, watch, setError, formState, formState: { errors } } = useForm({
    mode: "onTouched",
    resolver: yupResolver(userSchema),
  })

  const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState                   

  const {setUser, setToken} = useStateContext()


  const onSubmit = (el) => {
    const payload = {
      name:el.name,
      email:el.email,
      password:el.password,
      password_confirmation:el.password_confirmation,
    }
    
    axiosClient.post('/signup', payload)
    .then(({data}) => {
      setUser(data.user);
      setToken(data.token);
    })
    .catch(err => {
      console.log(err)
      const response = err.response
      if (response && response.status === 422) {
        console.log(response.data.errors);
        setError('server', { 
          type: response.status,
          message: response.data.message
        });      
      }
    })
  }

  return (
    <div className='login-signup-form animated fadeInDown'>
      <div className='form'>
        <form onSubmit={handleSubmit(onSubmit)}>
          <h1 className='title'> Signup for free </h1>
          {errors.server && <div className='alert'>
            {<p> {errors.server?.message} </p>} 
          </div>}
          <input {...register('name')} type="text" placeholder='Full Name'/>
          {errors.name && <div className='errors'> error : {errors.name?.message} </div>}
          <input {...register('email')} type="email" placeholder='Email Adress'/>
          {errors.email && <div className='errors'> error : {errors.email?.message} </div>}
          <input {...register('password')} type="password" placeholder='password'/>
          {errors.password && <div className='errors'> error : {errors.password?.message} </div>}
          <input {...register('password_confirmation')} type="password" placeholder='password Confirmation'/>
          {errors.password_confirmation && <div className='errors'> error : {errors.password_confirmation?.message} </div>}
          <button className='btn btn-block' disabled={isSubmitting || !isValid}> Signup </button>
          <p className='message'> 
            Already Registred ? <Link to="/login"> Sign In account </Link>
          </p>
        </form>
      </div>
    </div> 
  )
}
