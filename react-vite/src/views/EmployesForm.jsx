import React, { useState } from "react";
import { useForm, Controller } from "react-hook-form";

import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { 
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Box,
  useMediaQuery,
  Button,
  TextField,
  RadioGroup,
  Radio,
  FormControlLabel,
  FormLabel
} from "@mui/material";

import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";
import axiosClient from "../axios-client";
import { useNavigate } from "react-router-dom";
import dayjs from "dayjs";

export default function EmployeForm() {

  const [serverError, setServerError] = useState(null)

  const useScheme = yup.object({
    nom: yup.string().required("le champ nom est obligatoire"),
    prenom: yup.string().required("le champ prenom est obligatoire"),
    fonction: yup.string().required("le champ fonction est obligatoire"),
    sexe: yup.string().required("ce champ est obligatoire"),
    date_naissance: yup.date().required("le champs date de naissance est obligatoire"),
    date_recrutement: yup.date().required('le champ date de recrutement est obligatoire'),
    temp_occuper: yup.string().required('ce champ est obligatoire'),
    contract: yup.string().required('le champs contract est obligatoire'),
    handicape: yup.boolean().required()
  })
  const { handleSubmit, control, setError, register, formState, formState: { errors } } = useForm({
    mode: "onTouched",
    resolver: yupResolver(useScheme)
  });

  const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState                   
  const isNonMobile = useMediaQuery("(min-width:600px)");
  const navigate = useNavigate();

  const onSubmit = (data) => {
    const employe = {
      nom: data.nom,
      prenom: data.prenom,
      fonction: data.fonction,
      sexe: data.sexe,
      date_naissance: dayjs(data.date_naissance).format("YYYY-MM-DD"),
      date_recrutement: dayjs(data.date_recrutement).format("YYYY-MM-DD"),
      contract: data.contract,
      temp_occuper: data.temp_occuper,
      handicape: data.handicape
    }
    // console.log(employe)
    axiosClient.post('/employes', employe)
    .then(() => {
      console.log('success')
      navigate('/employes')
    })
    .catch((err) => {
      console.log(err)
      const response = err.response;
      if (response && response.status === 422) {
        // setError('server', { 
        //   type: response.status,
        //   message: response.data.errors
        // });
        // console.log(errors.server[0])
        setServerError(response.data.errors)
      }
    })
  }


  return (
    <>
      <div className="card animated fadeInDown">
        <Box m="20px">
          <form onSubmit={handleSubmit(onSubmit)}>
            <Box 
              display="grid" 
              gap="30px" 
              gridTemplateColumns="repeat(6, minmax(0, 1fr))"
              sx={{
                  "& > div": {gridColumn: isNonMobile ? undefined : "span 4"},    
              }}>

            <TextField
              label="Nom"
              variant="outlined"
              {...register("nom", { required: true })}
              sx={{ gridColumn: "span 2" }}
              error={errors.nom ? true : false}
              helperText={errors.nom && errors.nom?.message}
            />
            <TextField
              label="Prénom"
              variant="outlined"
              {...register("prenom", { required: true })}
              sx={{ gridColumn: "span 2" }}
              error={errors.prenom ? true : false}
              helperText={errors.prenom && errors.prenom?.message}
            />

            <TextField
              label="Fonction"
              variant="outlined"
              {...register("fonction", { required: true })}
              sx={{ gridColumn: "span 2" }}
              error={errors.fonction ? true : false}
              helperText={errors.fonction && errors.fonction?.message}
            />
                
            <Controller
              control={control}
              name="date_naissance"
              rules={{ required: true }}
              render={({ field: { onChange } }) => (
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DatePicker
                sx={{ gridColumn: "span 2" }}
                  onChange={(event) => { onChange(event)}}
                  format="DD/MM/YYYY"
                  label="date de naissance" 
                  slotProps={{
                    textField: {
                      error: errors.date_naissance ? true : false,
                      helperText: errors.date_naissance?.message 
                    }
                  }}
                />
              </LocalizationProvider>
              )}
            />

            <Controller
              control={control}
              name="date_recrutement"
              rules={{ required: true }}
              render={({ field: { onChange } }) => (
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                <DatePicker
                sx={{ gridColumn: "span 2" }}
                  format="DD/MM/YYYY"
                  onChange={(event) => { onChange(event)}}
                  label="date de recrutement" 
                  slotProps={{
                    textField: {
                      error: errors.date_recrutement ? true : false,
                      helperText: errors.date_recrutement?.message 
                    }
                  }}
                />
              </LocalizationProvider>
              )}
            />

            <FormControl variant="outlined" 
              sx={{ gridColumn: "span 2" }}>
              <InputLabel id="demo-simple-select-label"> Sexe </InputLabel>
              <Select
                label="Sexe"
                defaultValue={""}
                {...register("sexe")}
                error={errors.sexe ? true : false}

              >
                <MenuItem value="Femme"> Femme </MenuItem>
                <MenuItem value="Homme"> Homme </MenuItem>
              </Select>
              {errors.sexe && <span style={{
                color: "#d32f2f",
                fontSize: "0.75em",
                textAlign: "left",
                fontWeight: "400"
              }}> {errors.sexe?.message} </span>}
            </FormControl>

            <FormControl variant="outlined" 
              sx={{ gridColumn: "span 2" }}>
              <InputLabel> Temp Occuper </InputLabel>
              <Select
                label="Temp Occuper"
                defaultValue={""}
                {...register("temp_occuper")}
                error={errors.temp_occuper ? true : false}

              >
                <MenuItem value="Temps plein"> Temps plein </MenuItem>
                <MenuItem value="Temps partiel"> Temps partiel </MenuItem>
              </Select>
              {errors.temp_occuper && <span style={{
                color: "#d32f2f",
                fontSize: "0.75em",
                textAlign: "left",
                fontWeight: "400"
              }}> {errors.temp_occuper?.message} </span>}
            </FormControl>

            <FormControl variant="outlined" 
              sx={{ gridColumn: "span 2" }}>
              <InputLabel> contract </InputLabel>
              <Select
                label="contract"
                defaultValue={""}
                {...register("contract")}
                error={errors.contract ? true : false}

              >
                <MenuItem value="CDI"> CDI </MenuItem>
                <MenuItem value="CDD"> CDD </MenuItem>
              </Select>
              {errors.sexe && <span style={{
                color: "#d32f2f",
                fontSize: "0.75em",
                textAlign: "left",
                fontWeight: "400"
              }}> {errors.contract?.message} </span>}

            </FormControl>

            <Controller
              control={control}
              defaultValue={false}
              name="handicape"
              render={({ field }) => (
                <RadioGroup {...field}
                  sx={{ gridColumn: "span 2" }}
                >
                  <FormControlLabel
                    value={false}
                    control={<Radio value={false}/>}
                    label="non-handicape"
                  />
                  <FormControlLabel
                    value={true}
                    control={<Radio value={true}/>}
                    label="handicape"
                  />
                  {errors.handicape && <span style={{
                    color: "#d32f2f",
                    fontSize: "0.75em",
                    textAlign: "left",
                    fontWeight: "400"
                  }}> {errors.handicape?.message} </span>}
                </RadioGroup>
              )}
            />     
      
            </Box>   
            <Box display="flex" justifyContent="end" mt="20px">
              <Button  type="submit" color="success" variant="contained">
                  Create New User
              </Button>
            </Box>
          </form> 
        </Box>
        {serverError &&
          <div className="alert">
            {Object.keys(serverError).map(key => (
              <p key={key}>{serverError[key][0]}</p>
            ))}
          </div>
        }
      </div>
    </>
  )
}
